<?php

namespace App\Services;

use App\Libraries\EcoCash\EcoCashApi;
use App\Libraries\Mpesa\MpesaApi;
use App\Libraries\SMS\SmsApi;
use App\Mail\ConfirmationMail;
use App\Models\Client;
use App\Models\OneTimeServiceItemSale;
use App\Models\OneTimeServicePaymentHistory;
use App\Models\OneTimeServicesItem;
use App\Models\ServiceAttribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PaymentService
{
    // -------------------------------------------------------------------------
    // Payment entry points
    // -------------------------------------------------------------------------

    public function processMpesa(Request $request): JsonResponse
    {
        $mpesa    = new MpesaApi();
        $response = $mpesa->C2BMpesa($request->mpesa_mobile, $request->total_sale_price);

        if (is_null($response)) {
            return $this->paymentError('mpesa_mobile', 'Transaction failed. No response from M-PESA.');
        }

        $body = json_decode($response->body, true);

        if ($body['output_ResponseCode'] !== 'INS-0') {
            return $this->paymentError('mpesa_mobile', $body['output_ResponseDesc']);
        }

        $transactionId = $body['output_ThirdPartyConversationID'];

        return $this->commitAndRespond(
            $request,
            fn (int $clientId, string $ref) => [
                'client_id'    => $clientId,
                'collected_by' => 'online',
                'amount'       => $request->total_sale_price,
                'attachment'   => '',
                'reference_no' => $transactionId,
                'fine'         => 0,
                'pay_via'      => 'mpesa',
                'remarks'      => "$ref Service {$request->mpesa_mobile} paid via M-PESA Trx ID $transactionId",
                'status'       => 1,
            ],
            $body['output_ResponseDesc']
        );
    }

    public function processEcoCash(Request $request): JsonResponse
    {
        $service  = OneTimeServicesItem::with('oneTimeService.emails')->find($request->serviceItem);
        $ecoCash  = new EcoCashApi();
        $response = $ecoCash->getEcoCashResponse(
            $request->ecocash_mobile,
            $request->total_sale_price,
            $request->national_identity,
            $service->name
        );

        if (!$response) {
            return $this->paymentError('ecocash_mobile', 'Transaction failed. No response from EcoCash.');
        }

        if (isset($response->txnstatus) || !isset($response->request_id)) {
            return $this->paymentError('ecocash_mobile', $response->message ?? 'EcoCash transaction failed.');
        }

        $transactionId = $response->request_id;

        return $this->commitAndRespond(
            $request,
            fn (int $clientId, string $ref) => [
                'client_id'    => $clientId,
                'collected_by' => 'online',
                'amount'       => $request->total_sale_price,
                'attachment'   => '',
                'reference_no' => $transactionId,
                'fine'         => 0,
                'pay_via'      => 'ecocash',
                'remarks'      => "$ref Service paid via EcoCash Trx ID $transactionId",
                'status'       => 1,
            ],
            $response->message ?? 'Payment successful'
        );
    }

    public function processCreditCard(Request $request): JsonResponse
    {
        if ($request->Lite_Payment_Card_Status != '0') {
            return $this->paymentError('bank_card', $request->Lite_Result_Description);
        }

        $amount        = $request->Lite_Order_Amount / 100;
        $transactionId = $request->Ecom_ConsumerOrderID;

        return $this->commitAndRespond(
            $request,
            fn (int $clientId, string $ref) => [
                'client_id'    => $clientId,
                'collected_by' => 'online',
                'amount'       => $amount,
                'attachment'   => '',
                'reference_no' => $transactionId,
                'fine'         => 0,
                'pay_via'      => 'credit_card',
                'remarks'      => "$ref Service paid via credit card Trx ID $transactionId",
                'status'       => 1,
            ],
            $request->Lite_Result_Description
        );
    }

    // -------------------------------------------------------------------------
    // Core transaction wrapper
    // -------------------------------------------------------------------------

    /**
     * Create client + sale, record payment history, commit, then notify.
     *
     * @param  callable(int $clientId, string $referenceNumber): array  $paymentData
     */
    private function commitAndRespond(Request $request, callable $paymentData, string $message): JsonResponse
    {
        try {
            DB::beginTransaction();

            $client          = $this->createClient($request);
            $referenceNumber = 'ECoL-' . time();

            $this->createSale($request, $client->id, $referenceNumber);

            OneTimeServicePaymentHistory::create(
                $paymentData($client->id, $referenceNumber)
            );

            DB::commit();

            $this->sendConfirmation($client->id, $request->serviceItem, $referenceNumber);
            $this->sendSms($request, $referenceNumber);

            return response()->json([
                'status'           => 1,
                'message'          => $message,
                'client'           => $client->id,
                'reference_number' => $referenceNumber,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment transaction failed: ' . $e->getMessage());
            return response()->json(
                ['errors' => ['general' => ['An unexpected error occurred. Please try again.']]],
                500
            );
        }
    }

    // -------------------------------------------------------------------------
    // Client & sale persistence
    // -------------------------------------------------------------------------

    private function createClient(Request $request): Client
    {
        $client                    = new Client();
        $client->first_name        = $request->first_name;
        $client->last_name         = $request->last_name;
        $client->email             = $request->email;
        $client->phone             = $request->phone;
        $client->national_identity = $request->national_identity;
        $client->save();

        return $client;
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    private function createSale(Request $request, int $clientId, string $referenceNumber): void
    {
        $validator = Validator::make($request->all(), [
            '*.file' => 'sometimes|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        $item = OneTimeServicesItem::findOrFail($request->serviceItem);

        OneTimeServiceItemSale::create([
            'client_id'                 => $clientId,
            'requirements'              => json_encode($this->buildRequirements($request, $referenceNumber)),
            'price'                     => $request->total_sale_price,
            'reference_number'          => $referenceNumber,
            'one_time_services_id'      => $request->service,
            'financial_year'            => $item->financial_year,
            'one_time_services_item_id' => $request->serviceItem,
        ]);
    }

    private function buildRequirements(Request $request, string $referenceNumber): array
    {
        $requirements = [];

        foreach ($request->allFiles() as $key => $file) {
            $fileName           = 'Service-' . time() . '-' . $file->getClientOriginalName();
            $filePath           = $file->storeAs("services/$referenceNumber", $fileName, 'public');
            $requirements[$key] = "/storage/$filePath";
        }

        $codes = ServiceAttribute::where([
            ['one_time_service_id', '=', $request->service],
            ['frontend_type',       '!=', 'file'],
        ])->pluck('code');

        foreach ($codes as $code) {
            $value              = $request->get($code);
            $requirements[$code] = ($value === 'date-of-birth')
                ? date('Y-m-d H:i:s', strtotime($value))
                : $value;
        }

        return $requirements;
    }

    // -------------------------------------------------------------------------
    // Notifications
    // -------------------------------------------------------------------------

    private function sendConfirmation(int $clientId, int $serviceItemId, string $referenceNumber): void
    {
        $item   = OneTimeServicesItem::with('oneTimeService.emails')->find($serviceItemId);
        $emails = $item->oneTimeService->emails->pluck('email')->toArray();
        $client = Client::find($clientId);

        Mail::to($client->email)
            ->cc($emails)
            ->send(new ConfirmationMail($client, $item, $referenceNumber));
    }

    private function sendSms(Request $request, string $referenceNumber): void
    {
        $service = OneTimeServicesItem::find($request->serviceItem);
        $message = "Dear {$request->last_name} {$request->first_name},\n"
            . "Thank you for requesting {$service->description} with ECoL.\n"
            . "Your reference number is: $referenceNumber\n"
            . "You will be notified once the service is completed.";

        try {
            SmsApi::message($request->phone, $message);
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function paymentError(string $field, string $message): JsonResponse
    {
        return response()->json(['errors' => [$field => [$message]]], 422);
    }
}