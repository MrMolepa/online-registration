<?php

namespace App\Http\Controllers\Admin\FunWalk;

use App\Http\Controllers\Controller;
use App\Models\FunWalkPayment;
use App\Models\FunWalkRegistration;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Libraries\Mpesa\MpesaApi;
use App\Libraries\EcoCash\EcoCashApi;
use App\Libraries\SMS\SmsApi;

class FunWalkPaymentController extends Controller
{
    /**
     * Display a listing of payments (DataTables JSON)
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $payments = FunWalkPayment::with('registration.funWalk')
                ->select(['id', 'registration_id', 'amount', 'payment_method', 'transaction_ref', 'status', 'paid_at', 'created_at', 'updated_at']);

            return DataTables::of($payments)
                ->addColumn('registration_ticket', function ($p) {
                    return $p->registration ? $p->registration->ticket_number : '-';
                })
                ->addColumn('registration_full_name', function ($p) {
                    return $p->registration ? $p->registration->full_name : '-';
                })
                ->editColumn('amount', function ($p) {
                    return 'M' . number_format($p->amount, 2);
                })
                ->editColumn('status', function ($p) {
                    $statusClass = $p->status === 'completed' ? 'success' : ($p->status === 'failed' ? 'danger' : 'warning');
                    return '<span class="label label-'.$statusClass.'">'.ucfirst($p->status).'</span>';
                })
                ->editColumn('paid_at', function ($p) {
                    return $p->paid_at ? $p->paid_at->format('d M Y H:i:s') : '-';
                })
                ->editColumn('created_at', function ($p) {
                    return $p->created_at->format('d M Y H:i:s');
                })
                ->rawColumns(['status'])
                ->make(true);
        }

        return view('admin.fun-walk-payments.index');
    }

    /**
     * Process payment transaction
     */
    public function processPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'registration_id' => 'required|exists:fun_walk_registrations,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:Mpesa,EcoCash,E-Payment',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $registration = FunWalkRegistration::with('funWalk')->find($request->registration_id);
        $payment_method = $request->payment_method;

        switch ($payment_method) {
            case 'Mpesa':
                return $this->processMpesa($request, $registration);
                
            case 'EcoCash':
                return $this->processEcoCash($request, $registration);
                
            case 'E-Payment':
                return $this->processEPayment($request, $registration);
                
            default:
                return response()->json(['errors' => ['payment_method' => ['Invalid payment method']]]);
        }
    }

    /**
     * Process M-Pesa payment
     */
    private function processMpesa(Request $request, FunWalkRegistration $registration)
    {
        $validator = Validator::make($request->all(), [
            'mpesa_mobile' => 'required|digits:8'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        ob_start();
        $mpesa = new MpesaApi();
        $mpesa_api = $mpesa->C2BMpesa(
            $request->mpesa_mobile, 
            $request->amount,
            "FW-{$registration->ticket_number}",
            "Fun Walk Registration - {$registration->ticket_number}"
        );
        ob_end_clean();

        if (!is_null($mpesa_api)) {
            $mpesa_body = json_decode($mpesa_api->body, true);
            
            if ($mpesa_body['output_ResponseCode'] == 'INS-0') {
                $thirdPartyConversationID = $mpesa_body['output_ThirdPartyConversationID'];
                
                // Create payment record
                $payment = FunWalkPayment::create([
                    'registration_id' => $registration->id,
                    'amount' => $request->amount,
                    'payment_method' => 'Mpesa',
                    'transaction_ref' => $thirdPartyConversationID,
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);

                // Send SMS confirmation
                $message = "Dear {$registration->first_name} {$registration->last_name},\n" .
                          "Your payment of M{$request->amount} for Fun Walk ticket {$registration->ticket_number} has been received.\n" .
                          "Transaction Ref: {$thirdPartyConversationID}";
                SmsApi::message($registration->phone, $message);

                return response()->json([
                    'success' => true,
                    'message' => $mpesa_body['output_ResponseDesc'],
                    'payment' => $payment,
                    'ticket_number' => $registration->ticket_number
                ]);
            } else {
                return response()->json([
                    'errors' => ['mpesa_mobile' => [$mpesa_body['output_ResponseDesc']]]
                ]);
            }
        } else {
            return response()->json([
                'errors' => ['mpesa_mobile' => ['Transaction Failed']]
            ]);
        }
    }

    /**
     * Process EcoCash payment
     */
    private function processEcoCash(Request $request, FunWalkRegistration $registration)
    {
        $validator = Validator::make($request->all(), [
            'ecocash_mobile' => 'required|digits:8'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        ob_start();
        $ecoCashApi = new EcoCashApi();
        $ecoCashResponse = $ecoCashApi->getEcoCashResponse(
            $request->ecocash_mobile,
            $request->amount,
            "FW-{$registration->ticket_number}",
            "Fun Walk Registration - {$registration->ticket_number}"
        );
        ob_end_clean();

        if ($ecoCashResponse) {
            if (!isset($ecoCashResponse->txnstatus) && isset($ecoCashResponse->extra_data) && isset($ecoCashResponse->request_id)) {
                $thirdPartyConversationID = $ecoCashResponse->request_id;
                
                // Create payment record
                $payment = FunWalkPayment::create([
                    'registration_id' => $registration->id,
                    'amount' => $request->amount,
                    'payment_method' => 'EcoCash',
                    'transaction_ref' => $thirdPartyConversationID,
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);

                // Send SMS confirmation
                $message = "Dear {$registration->first_name} {$registration->last_name},\n" .
                          "Your payment of M{$request->amount} for Fun Walk ticket {$registration->ticket_number} has been received.\n" .
                          "Transaction Ref: {$thirdPartyConversationID}";
                SmsApi::message($registration->phone, $message);

                return response()->json([
                    'success' => true,
                    'message' => $ecoCashResponse->message,
                    'payment' => $payment,
                    'ticket_number' => $registration->ticket_number
                ]);
            } else {
                return response()->json([
                    'errors' => ['ecocash_mobile' => [$ecoCashResponse->message]]
                ]);
            }
        } else {
            return response()->json([
                'errors' => ['ecocash_mobile' => ['Transaction Failed']]
            ]);
        }
    }

    /**
     * Process E-Payment (Credit Card)
     */
    private function processEPayment(Request $request, FunWalkRegistration $registration)
    {
        //  credit card payment logic 
        
        return response()->json([
            'success' => true,
            'message' => 'E-Payment processing initiated',
            'redirect_url' => route('payment.gateway') // payment gateway route
        ]);
    }
}