<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\oneTimeServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Invoice;
use App\Models\Payment as PaymentModel;
use Illuminate\Support\Facades\DB;
use App\Libraries\fpdf\easyTable;
use App\Libraries\fpdf\exFPDF;
use App\Libraries\Mpesa\MpesaApi;
use App\Mail\ConfirmationMail;
use App\Models\Candidate;
use App\Models\Center;
use App\Models\Level;
use App\Models\Client;
use App\Models\OneTimeService;
use App\Models\OneTimeServiceItemSale;
use App\Models\OneTimeServicesItem;
use App\Models\ServiceAttribute;
use App\Models\Setting;
use Dflydev\DotAccessData\Data;
use GrahamCampbell\ResultType\Success;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;




class ServiceController extends Controller
{
    //
    public function index()
    {
       $services = OneTimeService::with('OneTimeServicesItem')->where('status','=',1)->get();
        return view('service.services', compact('services'));
    }
    public function getOneTimeServicesItem(Request $request)
    {

        $html = "";
        if ($request->service == "status") {
            $html .= "<div class='form__field'>
                        <label for='reference_no'>
                            Reference number
                            <span data-required='true' aria-hidden='true'></span>
                        </label>
                        <input id='reference_no' type='text'
                            name='reference_no' placeholder='Please Enter reference number'
                            autocomplete='reference_no'>
                            <div class='statuses-container mt-2 card'>

                            </div>
                            <button type='button' class='btn btn-primary btn-lg btn-block' id='check-status'>
                                    Check
                            </button>
                      </div>";
        } else {
            $financial_year =(date('m') <= 3)?   (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
            $oneTimeServices = OneTimeService::with('OneTimeServicesItem')
            ->whereHas('OneTimeServicesItem', function($query) use ($financial_year){
                $query->where('financial_year', '=',$financial_year );
            })
            ->findOrFail($request->service);
            $oneTimeServicesItems =is_null($oneTimeServices)?array(): $oneTimeServices->OneTimeServicesItem;
            foreach ($oneTimeServicesItems as  $oneTimeServicesItem) {
                if ($oneTimeServicesItem->financial_year==$financial_year) {
                    $html .= "<div class='form__radio'>
                            <label for=' $oneTimeServicesItem->name'>
                                $oneTimeServicesItem->name  (M $oneTimeServicesItem->price.00)</label>
                            <input id='$oneTimeServicesItem->name'  data-price='$oneTimeServicesItem->price' data-service='$oneTimeServicesItem->name' data-id='$request->service' name='serviceItem' value='$oneTimeServicesItem->id' type='radio' />
                        </div>
                        ";
                }

            }
        }

        return response()->json(['html' =>  $html]);
    }

    public function serviceRequirements(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        // $reference_number = 'BFF-' . time()
       $levels = Level::where('is_active', '=', 1)->get();
        $serviceAttributes = ServiceAttribute::where(['one_time_service_id' => $request->service])->get();
        $attributesHTML = view('service.requirements.requirements', compact('serviceAttributes', 'levels'))->render();
        $personalInfoHTML = view('service.personal-info.personal-info')->render();
        $paymentsHTML = view('service.payment.payment')->render();
        return response()->json([
            'attributesHTML' => $attributesHTML,
            'personalInfoHTML' => $personalInfoHTML,
            'paymentsHTML' =>  $paymentsHTML,
            'client' => $request->all(),
            'serviceAttributes' => $serviceAttributes
        ]);
    }


    public function searchCandidate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $candidate = Candidate::find($request->search);
        if (!empty($candidate)) {
            return response()->json($candidate);
        } else {
            $candidate = new Candidate();
            $table = $candidate->getTable();
            $columns  = Schema::getColumnListing($table);
            $collection = new Collection();
            $candidateArray = array();
            foreach ($columns as  $column) {
                $candidateArray[$column] = "";
            }
            $collection->push((object) $candidateArray);
            return response()->json($collection->first());
        }
    }

     public function validCandidate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'candidate_no' => ['required', 'exists:candidates,candidate_no']
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $candidate = Candidate::find($request->candidate_no);
        if (!isset($candidate)) {
            return response()->json(['errors' => 'Candidate number is Invalid'], 401);
        }
        return response()->json($candidate ,200);
    }


    public function checkStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference_no' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $OneTimeServiceItemSales =
            DB::table('one_time_services_item_sale')
            ->select(
                'clients.first_name',
                'clients.last_name',
                'clients.email',
                'clients.phone',
                'clients.national_identity',
                'one_time_services_item.name',
                'one_time_services_item_sale.id',
                'one_time_services_item_sale.one_time_services_id',
                'one_time_services_item_sale.price',
                'one_time_services_item_sale.financial_year',
                'one_time_services_item_sale.requirements',
                'one_time_services_item_sale.reference_number',
                'one_time_services_item_sale.is_checked',
                'one_time_services_item_sale.updated_at'
            )
            ->join('clients', 'one_time_services_item_sale.client_id', '=', 'clients.id')
            ->join('one_time_services_item', function ($join) {
                $join->on('one_time_services_item_sale.one_time_services_item_id', '=', 'one_time_services_item.id');
                $join->on('one_time_services_item_sale.financial_year', '=', 'one_time_services_item.financial_year');
            })->where('reference_number', '=', $request->reference_no)
            ->orWhere('national_identity', '=', $request->reference_no)
            ->get();
        if ($OneTimeServiceItemSales->isNotEmpty()) {
            $firstName = strtoupper($OneTimeServiceItemSales->first()->first_name);
            $lastName = strtoupper($OneTimeServiceItemSales->first()->last_name);
            $statusHTML = "<div class='card-header'>
                                $firstName $lastName
                            </div>
                            <ul class='list-group list-group-flush'>";
            foreach ($OneTimeServiceItemSales as  $OneTimeServiceItemSale) {
                $status = $OneTimeServiceItemSale->is_checked * 33;
                $date = date('Y-m-d', strtotime($OneTimeServiceItemSale->updated_at));
                $statusHTML .= "<a href='#' class='list-group-item list-group-item-action'>$OneTimeServiceItemSale->name</a>
                                <a href='#' class='list-group-item list-group-item-action'>
                                    <div class='status-progress-wrap'>
                                            <div class='status-progress-bar-wrap'>
                                                <div style='width:$status%;' class='status-progress-bar'>
                                                </div>
                                            </div>
                                            <div class='status-wrap'>
                                                Pending
                                                <span> $date</span>
                                            </div>
                                            <div class='status-wrap'>
                                                Checked
                                                <span> $date</span>
                                            </div>
                                            <div class='status-wrap'>
                                                Completed
                                                <span> $date</span>
                                            </div>
                                    </div>
                                </a>
                              ";
            }
            $statusHTML  .= "</ul>";
            return response()->json(['status' =>  $statusHTML]);
        } else {
            return response()->json(['errors' => ['reference_no' => ['These record do not match our records. ']], 'test' => $OneTimeServiceItemSales]);
        }
    }
    public function searchCenter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'candidate_no' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $candidate = new Candidate();
        $table = $candidate->getTable();
        $columns  = Schema::getColumnListing($table);
        return response()->json($columns);
    }
    private function register($request)
    {
        $client = new Client();
        $client->first_name = $request->first_name;
        $client->last_name = $request->last_name;
        $client->email = $request->email;
        $client->phone = $request->phone;
        $client->national_identity = $request->national_identity;
        $client->save();
        $client_id = $client->id;
        $requirementsArray = array();
        $whereData = array(array('one_time_service_id', '=', "$request->service"), array('frontend_type', '!=', 'file'));
        $serviceAttributes = ServiceAttribute::where($whereData)->pluck('code');
        $files = $request->allFiles();
        $reference_number = 'ECoL-' . time();
        if (!empty($files)) {
            foreach ($files as $key => $file) {
                $extension =   $request->file("$key")->getClientOriginalExtension();
                if (strtolower($extension) == "pdf") {
                    $fileName =  'Service' . '-' . time() . '-' . $request->file("$key")->getClientOriginalName();
                    $filePath = $request->file("$key")->storeAs('services/' .  $reference_number, $fileName, 'public');
                    $requirementsArray[$key] =  "/storage/$filePath";
                } else {
                    $fileName =  'Service' . '-' . time() . '-' . $request->file("$key")->getClientOriginalName();
                    $filePath = $request->file("$key")->storeAs('services/' .  $reference_number, $fileName, 'public');

                    $requirementsArray[$key] =  "/storage/$filePath";
                }
            }
        }
        foreach ($serviceAttributes as   $serviceAttribute) {
            if ($request->get("$serviceAttribute") == "date-of-birth") {
                $requirementsArray[$serviceAttribute] = date("Y-m-d H:i:s", strtotime($request->get("$serviceAttribute")));
            } else {
                $requirementsArray[$serviceAttribute] = $request->get("$serviceAttribute");
            }
        }




        $oneTimeServiceItem = OneTimeServicesItem::find($request->serviceItem);
        $financial_year =$oneTimeServiceItem->financial_year;

        $input = [
            'client_id' => $client_id,
            'requirements' => json_encode($requirementsArray),
            'price' => $request->total_sale_price,
            'reference_number' => $reference_number,
            'one_time_services_id' => $request->service,
            'financial_year' => $financial_year,
            'one_time_services_item_id' => $request->serviceItem,
        ];
        OneTimeServiceItemSale::create($input);
        $collection = new Collection();
        $collection->push((object)[
            'client' =>  $client_id,
            'service' => $request->service,
            'reference_number' => $reference_number,
            'financial_year' => $financial_year,
        ]);
        return    $collection->first();
    }
    public function multiform(Request $request)
    {
        $currentPage = 2;
        $validationRules = [
            1 => [
                'first_name' => ['required'],
                'last_name' => ['required'],
                'phone_no' => ['required'],
                'email' => ['required', 'email'],
                'address' => ['required'],
            ],
            2 => [],
        ];
        $validationMassages = [
            1 => [],
            2 => [],
        ];
        $serviceAttributes = ServiceAttribute::where(['one_time_service_id' => 3])->get();
        foreach ($serviceAttributes as  $serviceAttribute) {
            $validationRules[2][$code = $serviceAttribute->code] = array('required');
        }
        $validator = Validator::make($request->all(), $validationRules[$currentPage],  $validationMassages[$currentPage]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
    }
    public function paymentTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'national_identity' => 'required',
            'phone' => 'required|numeric',
            'email' => 'required|email',
            'serviceItem' => 'required',
            'total_sale_price' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }



        $payment = $request->payment;
        switch ($payment) {
            case 'CreditCard':
                // payment
                $status = 0;
                if ($request->Lite_Payment_Card_Status == "0") {
                    $ecom_ConsumerOrderID =  $request->Ecom_ConsumerOrderID;
                    $amount =  $request->Lite_Order_Amount;
                    $amount =    $amount / 100;
                    $register = $this->register($request);
                    $client_id = $register->client;
                    $reference_number = $register->reference_number;
                    $financial_year =$register->financial_year;

                    $invoice = new Invoice();
                    $invoice->client_id = $client_id;
                    $invoice->national_id=$request->national_identity;
                    $invoice->level = "E-Service";
                    $invoice->session = "E-Service";
                    $invoice->service = "E-Service";
                    $invoice->financial_year =  $financial_year;  //date('Y') . '-' . (date('Y') + 1);
                    $invoice->reference_no =  $ecom_ConsumerOrderID;
                    $invoice->amount = $amount;
                    $invoice->save();
                    $invoiceid = $invoice->id;
                    PaymentModel::create([
                        "invoice_id" =>  $invoiceid,
                        "reference_no" => $ecom_ConsumerOrderID,
                        "amount" => $amount,
                    ]);
                    $status = 1;
                    $this->sendConfirmation($client_id, $request->serviceItem, $reference_number);
                    return response()->json([
                        'status' =>  $status,
                        'client' => $client_id,
                        'reference_number' => $reference_number,
                        'message' => $request->Lite_Result_Description
                    ]);
                } else {
                    return response()->json(['errors' => ['bank_card' => array($request->Lite_Result_Description)]]);
                }
                break;
            case 'CashDeposit':
                // payment
                break;
            case 'VclMpesa':
                // payment
                $validator = Validator::make($request->all(), [
                    'mpesa_mobile' => 'required|digits:8'
                ]);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()]);
                }
                ob_start();
                $mpesa = new  MpesaApi();
                $mpesa_api = $mpesa->C2BMpesa($request->mpesa_mobile, $request->total_sale_price);
                ob_end_clean();
                $status = 0;
                if (!is_null($mpesa_api)) {
                    // convert json to array
                    $mpesa_body = json_decode($mpesa_api->body, true);
                    if ($mpesa_body['output_ResponseCode'] == 'INS-0') {
                        $thirdPartyConversationID =   $mpesa_body['output_ThirdPartyConversationID'];
                        $amount =  $request->total_sale_price;
                        $register = $this->register($request);
                        $client_id = $register->client;
                        $reference_number = $register->reference_number;
                        $financial_year =$register->financial_year;
                        $invoice = new Invoice();
                        $invoice->client_id = $client_id;
                         $invoice->national_id=$request->national_identity;
                        $invoice->level = "E-Service";
                        $invoice->session = "E-Service";
                        $invoice->service = "E-Service";
                        $invoice->financial_year =   $financial_year;
                        $invoice->reference_no =   $thirdPartyConversationID;
                        $invoice->amount = $amount;
                        $invoice->save();
                        $invoiceid = $invoice->id;
                        PaymentModel::create([
                            "invoice_id" =>  $invoiceid,
                            "reference_no" =>  $thirdPartyConversationID,
                            "amount" => $amount,
                        ]);
                        $status = 1;
                     $this->sendConfirmation($client_id, $request->serviceItem, $reference_number);
                          return response()->json([
                            'status' => $status,
                            'message' => $mpesa_body['output_ResponseDesc'],
                            'client' => $client_id,
                            'reference_number' => $reference_number
                            ]);
                        exit();
                    } else {
                        return response()->json(['errors' => ['mpesa_mobile' => array($mpesa_body['output_ResponseDesc'])]]);
                    }
                } else {
                    return response()->json(['errors' => ['mpesa_mobile' => array('Transaction Failed')]]);
                }
                break;
            default:
                break;
        }
    }
    public function autocompleteAllCentersSearch(Request $request)
    {
        // <!-- search centre -->
        $centers = [];
        if ($request->has('q')) {
            $center_name = $request->get('q');
            $centers = Center::where('center_name', 'LIKE', "{$center_name}%")
                ->limit(5)->get();
            return response()->json($centers);
        }
    }
    private function sendConfirmation($client, $serviceItem, $reference_number)
    {

        $emails = Setting::whereIn('meta_field', [
            'business_email',
            'finance_email',
            'verification_email'
        ])->pluck('meta_value')->toArray();

        $client = Client::find($client);
        $oneTimeServicesItem = OneTimeServicesItem::find($serviceItem);
        Mail::to($client->email)
            ->cc($emails)
            ->send(new ConfirmationMail($client, $oneTimeServicesItem, $reference_number));
    }
}
