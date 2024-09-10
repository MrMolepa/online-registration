<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Libraries\Mpesa\MpesaApi;
use App\Mail\CandidateInvoiceMail;
use App\Models\Address;
use App\Models\Candidate;
use App\Models\CandidateArrangement;
use App\Models\CandidateUser;
use App\Models\Center;
use App\Models\CenterCandidate;
use App\Models\Guardian;
use App\Models\GuardianType;
use App\Models\Invoice;
use App\Models\SpecialNeed;
use App\Models\SubjectCandidate;
use Illuminate\Http\Request;
use App\Models\Payment as PaymentModel;
use App\Models\Setting;
use App\Models\Subject;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Input\Input;

class PaymentController extends Controller
{
    public function index()
    {

        $districts = Center::groupBy('district_code')
            ->whereNotNull('district_code')->get();
        $national_id = auth()->user()->national_id;

        $subjects= DB::table('candidate_subject')
        ->select(
            [
                'center_candidate.id',
                'center_candidate.center_no',
                'centers.center_name',
                'center_candidate.candidate_no',
                'center_candidate.national_id',
                'center_candidate.session',
                'center_candidate.financial_year',
                'center_candidate.level',
                'center_candidate.type',
                'center_candidate.subject_number',
                'candidates.candidate_surname',
                'candidates.candidate_other_name',
                'candidates.date_of_birth',
                'candidates.gender',
                'center_candidate.sponser',
                'subjects.subject_code',
                'subjects.subject_name',
                'candidate_subject.subject_option'
            ],
        )->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
        ->join('subjects', 'subjects.subject_code', '=', 'candidate_subject.subject_code')
        ->join('center_candidate', function ($join) {
            $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
            $join->on('candidate_subject.level', '=', 'center_candidate.level');
            $join->on('candidate_subject.session', '=', 'center_candidate.session');
            $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
        })
        ->join('centers', 'centers.center_no', '=', 'center_candidate.center_no')
         ->groupBy('center_candidate.candidate_no','center_candidate.level', 'center_candidate.session','subjects.subject_code')
        ->where("center_candidate.national_id", '=', $national_id)
        ->where("center_candidate.financial_year", '=',date('Y') . '-' . (date('Y') + 1))
        ->where("center_candidate.session",'=',auth()->user()->session)
        ->get();


        $candidate =  $subjects->first();

        $amount_paid = Invoice::where([
            ['client_id', $candidate->candidate_no],
            ['financial_year' ,$candidate->financial_year],
            ['level' ,$candidate->level],
            ['national_id' ,$national_id],
           ['session' ,$candidate->session]
        ])->sum('amount');




        $schoolFees =    DB::table('fees_stracture')
            ->where('candidate_type', '=', 'lgcse-school')
            ->where('financial_year', '=', date('Y') . '-' . (date('Y') + 1))
            ->first();
        $schoolPrivate = DB::table('fees_stracture')
            ->where('candidate_type', '=', 'lgcse-private')
            ->where('financial_year', '=',  date('Y') . '-' . (date('Y') + 1))
            ->first();


        $practicalSubjects = ['0179', '0189', '0191', '0192', '0194', '0417', '0190'];
        $delf = Subject::whereHas('selectedDiscipline', function ($q) {
            $q->where('name', '=', 'LGCSE7');
        })->get()->pluck('subject_code')->toArray();
        $total_amount = 0;
        if (in_array($candidate->type, [2, 3])) {
            foreach ($subjects as $subject) {

                if (in_array($subject->subject_code, $practicalSubjects)) {
                    $total_amount += ($schoolPrivate->subject_fee) + $schoolPrivate->practical_subject_fee;
                }else if (in_array($subject->subject_code, $delf)) {
                    $total_amount += ($schoolPrivate->delf_fee);
                } else {
                    $total_amount += ($schoolPrivate->subject_fee);
                }
            }
            $total_amount  +=  $schoolPrivate->local_fee + $schoolPrivate->registration_fee;
        } else {
            foreach ($subjects as $subject) {
                if (in_array($subject->subject_code, $practicalSubjects)) {
                    $total_amount += ($schoolFees->subject_fee) + $schoolFees->practical_subject_fee;
                }else if (in_array($subject->subject_code, $delf)) {
                    $total_amount += ($schoolFees->delf_fee);
                } else {
                    $total_amount += ($schoolFees->subject_fee);
                }
            }
            $total_amount  +=  $schoolFees->local_fee + $schoolFees->registration_fee;
        }


        $subject_number = count($subjects);
        $specialNeeds = SpecialNeed::get();
        $guardian_types =  GuardianType::get();
        $total_amount  =  $total_amount- $amount_paid;


         $is_paid = DB::table('late_fees')
            ->select('*')
            ->where('financial_year', '=', $candidate->financial_year)
            ->where('session', '=', $candidate->session)
            ->where('amount', '=',  abs($total_amount))
            ->first();
        if ($is_paid != null) {
            $total_amount += $is_paid->amount;
        }


          $late_fee = DB::table('late_fees')
                ->select('*')
                ->whereDate('start_date', '<=', date("Y-m-d"))
                ->whereDate('end_date', '>=', date("Y-m-d"))
                ->first();

        if( $total_amount >$schoolFees->subject_fee && $late_fee!==null ){
          $total_amount +=$late_fee->amount;
        }



        return view('candidate.payment', compact('districts', 'subjects', 'candidate', 'specialNeeds', 'guardian_types', 'total_amount', 'subject_number'));
    }



    public function paymentTransaction(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'payment' => 'required',
            'total_amount' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $candidateNo  =  $request->candidate_no;
        $payment = $request->payment;
        $session = $request->session;
        $level =  $request->level;
        $national_id = $request->national_id;
        switch ($payment) {
            case 'CreditCard':
                // payment
                $ecom_ConsumerOrderID =  $request->Ecom_ConsumerOrderID;
                $amount =  $request->Lite_Order_Amount;
                $amount =    $amount / 100;
                $status = 0;
                if ($request->Lite_Payment_Card_Status == "0") {
                    $invoice = new Invoice();
                    $invoice->national_id = $national_id;
                    $invoice->client_id =   $candidateNo;
                    $invoice->level = $level;
                    $invoice->session = $session;
                    $invoice->financial_year = date('Y') . '-' . (date('Y') + 1);
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
                    // Send  Email
                    $this->sendConfirmation($national_id, $candidateNo, $session, date('Y') . '-' . (date('Y') + 1));
                    $info = [
                        'centreNo' => $request->center_no,
                        'candidate_no' => $candidateNo,
                        'level' => $level,
                        'session' => $session,
                    ];
                    return response()->json(['status' => $status, 'message' => $request->Lite_Result_Description, 'output' =>  $info, 'publised' => is_publised($level, $session)]);
                } else {
                    return response()->json(['status' => $status, 'message' => $request->Lite_Result_Description]);
                }
                break;
            case 'CashDeposit':
                // payment

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
                $mpesa_api = $mpesa->C2BMpesa($request->mpesa_mobile, $request->total_amount);
                $status = 0;
                ob_end_clean();
                if (!is_null($mpesa_api)) {
                    // convert json to array
                    $mpesa_body = json_decode($mpesa_api->body, true);
                    if ($mpesa_body['output_ResponseCode'] == 'INS-0') {

                        $thirdPartyConversationID =   $mpesa_body['output_ThirdPartyConversationID'];
                        $amount =  $request->total_amount;
                        $invoice = new Invoice();
                        $invoice->national_id = $national_id;
                        $invoice->client_id =   $candidateNo;
                        $invoice->level = $level;
                        $invoice->session = $session;
                        $invoice->financial_year = date('Y') . '-' . (date('Y') + 1);
                        $invoice->reference_no = $thirdPartyConversationID;
                        $invoice->amount = $amount;
                        $invoice->save();
                        $invoiceid = $invoice->id;
                        PaymentModel::create([
                            "invoice_id" =>  $invoiceid,
                            "reference_no" =>  $thirdPartyConversationID,
                            "amount" => $amount,

                        ]);
                        $status = 1;
                        // Send  Email
                        $this->sendConfirmation($national_id, $candidateNo, $session, date('Y') . '-' . (date('Y') + 1));
                        $info = [
                            'centreNo' => $request->center_no,
                            'candidate_no' => $candidateNo,
                            'level' => $level,
                            'session' => $session,
                        ];
                        exit(json_encode(['status' => $status, 'message' => $mpesa_body['output_ResponseDesc'], 'output' =>  $info, 'publised' => is_publised($level, $session)]));
                    } else {
                        exit(json_encode(['errors' => array('mpesa_mobile' => array($mpesa_body['output_ResponseDesc']))]));
                    }
                } else {
                    exit(json_encode(['errors' => array('mpesa_mobile' => array('Transaction Failed'))]));
                }
                break;
            default:
                break;
        }
    }

    public function register(Request $request)
    {
        $candidate_no = $request->candidate_no;
        $session = $request->session;
        $financial_year = $request->financial_year;
        $national_id = $request->national_id;
        $center_no = $request->center_no;
        // Candidate Address
        try {
            CenterCandidate::where('candidate_no', '=', $candidate_no)
                ->where('national_id', '=',  $national_id)
                ->where('financial_year', '=',  $financial_year)
                ->where('session', '=',  $session)
                ->update([
                    'phone_number' => $request->candidate_phone_number,
                    'email' => $request->candidate_email
                ]);
            //Address
            Address::updateOrCreate(
                ['user_id' => $request->national_id, 'user_type' => Candidate::class],
                [
                    "postal_address" => $request->candidate_postal_address,
                    "physical_address" => $request->candidate_physical_address,
                    "village" => $request->candidate_village,
                    "user_id" => $request->national_id,
                    "user_type" => Candidate::class,
                    "district" => $request->candidate_district,
                ]
            );
            //  Special Needs
            $specilalNeed = SpecialNeed::find($request->special_need);
            // CandidateArrangement
            $CandidateArrangement = new CandidateArrangement();
            $CandidateArrangement->candidate_no = $candidate_no;
            $CandidateArrangement->arrangement_id = $specilalNeed->arrangement_id;
            $CandidateArrangement->save();
            // Guardian addresss
            Address::updateOrCreate(
                ['user_id' => $request->guardian_national_id, 'user_type' => Guardian::class],
                [
                    'user_id' => $request->guardian_national_id,
                    "postal_address" => $request->guardian_postal_address,
                    "physical_address" => $request->guardian_physical_address,
                    "village" => $request->guardian_village,
                    "user_id" => $request->guardian_national_id,
                    "user_type" => Guardian::class,
                    "district" => $request->guardian_district,
                ]
            );
            //Gurdian
            Guardian::updateOrCreate(
                [
                    'candidate' => $candidate_no,
                    'national_id' => $request->guardian_national_id
                ],
                [
                    "candidate" => $candidate_no,
                    'national_id' => $request->guardian_national_id,
                    "guardian_type" => $request->guardian_type,
                    "name" => $request->guardian_name,
                    "surname" => $request->guardian_surname,
                    "email" => $request->guardian_email,
                    "phone_number" => $request->guardian_phone_number
                ]
            );
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    private function sendConfirmation($national_id, $candidate_no, $session, $financial_year)
    {

        $candidate = DB::table('candidate_subject')
            ->select(
                [
                    'center_candidate.id',
                    'center_candidate.center_no',
                    'center_candidate.national_id',
                    'center_candidate.candidate_no',
                    'center_candidate.email',
                    'center_candidate.phone_number',
                    'center_candidate.session',
                    'center_candidate.level',
                    'center_candidate.type',
                    'center_candidate.subject_number',
                    'candidates.candidate_surname',
                    'candidates.candidate_other_name',
                    'candidates.date_of_birth',
                    'candidates.gender',
                    'center_candidate.sponser',
                    'invoices.amount',

                ],
            )->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('center_candidate', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
                $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
            })
            ->leftJoin('invoices', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'invoices.client_id');
                $join->on('candidate_subject.level', '=', 'invoices.level');
                $join->on('candidate_subject.session', '=', 'invoices.session');
                $join->on('candidate_subject.financial_year', '=', 'invoices.financial_year');
            })
            ->where("center_candidate.candidate_no", '=', $candidate_no)
            ->where("center_candidate.national_id", '=', $national_id)
            ->where("center_candidate.session", '=', $session)
            ->where("center_candidate.financial_year", '=', $financial_year)
            ->first();

        $email = $candidate->email;
        $amount = $candidate->amount;

        // $candidate=
        $emails = Setting::whereIn('meta_field', [
            //  'business_email',
            'finance_email',

        ])->pluck('meta_value')->toArray();
        Mail::to($email)
            ->cc($emails)
            ->send(new CandidateInvoiceMail($candidate, $amount));
    }
}
