<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Libraries\EcoCash\EcoCashApi;
use App\Libraries\Mpesa\MpesaApi;
use App\Libraries\fpdf\easyTable;
use App\Libraries\fpdf\exFPDF;
use App\Mail\CandidateInvoiceMail;
use App\Models\Address;
use App\Models\Candidate;
use App\Models\CandidateArrangement;
use App\Models\CandidateUser;
use App\Models\Center;
use App\Models\CenterCandidate;
use App\Models\FeeCandidateHistory;
use App\Models\FeeFine;
use App\Models\Guardian;
use App\Models\GuardianType;
use App\Models\SpecialNeed;
use Illuminate\Http\Request;
use App\Models\Setting;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function index()
    {


        $national_id = auth()->user()->national_id;
        $financial_year = auth()->user()->financial_year;
        $districts = Center::groupBy('district_code')
            ->whereNotNull('district_code')->get();

        $subjects = DB::table('candidate_subject')
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
                    DB::raw("COALESCE( (SELECT SUM(fee_candidate_histories.amount) FROM fee_candidate_histories  WHERE
                        fee_candidate_histories.candidate_id = center_candidate.id
                        ),0) AS  amount_paid"),
                    DB::raw("COALESCE( (SELECT SUM(fee_candidate_histories.fine) FROM fee_candidate_histories  WHERE
                        fee_candidate_histories.candidate_id = center_candidate.id
                        ),0) AS  fine"),
                    DB::raw("COALESCE((Select sum(fee_group_details.amount) from fee_groups
                        inner join fee_group_details on  fee_group_details.fee_group_id=fee_groups.id
                        inner join fee_types on  fee_types.id=fee_group_details.fee_type_id
                        inner join sessions on  sessions.id=fee_groups.session_id
                        inner join levels on  levels.id=fee_groups.level_id
                        where fee_groups.candidate_type=CASE center_candidate.type
                                                    WHEN 1 THEN 1
                                                    WHEN 2 THEN 3
                                                    ELSE 3
                                                    END
                        and sessions.session=center_candidate.session and
                         levels.level=center_candidate.level and
                         sessions.financial_year=center_candidate.financial_year
                        and  fee_group_details.subject_code in (
                        (SELECT  candidate_subject.subject_code  FROM `candidate_subject`
                        WHERE `candidate_subject`.`candidate_no`=`center_candidate`.`candidate_no` AND
                            `candidate_subject`.`level`=`center_candidate`.`level` AND
                            `candidate_subject`.`session`=`center_candidate`.`session` AND
                            `candidate_subject`.`financial_year`=`center_candidate`.`financial_year`
                            union SELECT '-' as subject_code)
                        )),0) as exam_fee"),
                    DB::raw("(Select fee_groups.id from fee_groups
                                    inner join fee_group_details on
                                    fee_group_details.fee_group_id=fee_groups.id
                                    inner join fee_types on fee_types.id=fee_group_details.fee_type_id
                                    inner join sessions on sessions.id=fee_groups.session_id
                                    inner join levels on levels.id=fee_groups.level_id
                                    where fee_groups.candidate_type=CASE center_candidate.type WHEN 1 THEN 1 WHEN 2 THEN 3 ELSE 3 END and
                                sessions.session=center_candidate.session and
                                levels.level=center_candidate.level and
                                sessions.financial_year=center_candidate.financial_year LIMIT 1) as fee_group_id"),
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
            ->where("center_candidate.national_id", '=', $national_id)
            ->where("center_candidate.financial_year", '=',  $financial_year)
            ->where("center_candidate.session", '=', auth()->user()->session)
            ->get();
        $candidate =  $subjects->first();


        if ($subjects->contains('subject_code', '2031')) {
              abort(403, 'Delf is not allowed. Please conduct Ecol.');
        }

        $amount_paid =  $candidate->amount_paid;
        $total_amount = $candidate->exam_fee + $candidate->fine;
        $groupId = $candidate->fee_group_id;
        $total_fine = 0;
        $fine = FeeFine::where('fee_group_id', '=', $groupId)
            ->where('start_date', '<=',   date('Y-m-d'))
            ->where('end_date', '>=',   date('Y-m-d'))
            ->first();

        $total_amount = $total_amount - $amount_paid;
        if ($total_amount > 0) {
            $total_fine = is_null($fine) ? 0 : $fine->fine_value;
            if ($total_amount > ($total_amount * 5 / 100)  &&   $fine !== null) {
                $total_amount += $total_fine;
            }
        }

        $subject_number = count($subjects);
        $specialNeeds = SpecialNeed::get();
        $guardian_types =  GuardianType::get();
        $installment = DB::table('installments')
            ->select('*')
            ->where("candidate_no", '=', $candidate->candidate_no)
            ->first();
        if ($installment !== null) {
            $total_amount = $installment->total_amount;
        }
        return view('candidate.payment', compact('districts', 'subjects', 'candidate', 'specialNeeds', 'guardian_types', 'total_amount', 'subject_number', 'total_fine'));
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
        $national_id = auth()->user()->national_id;
        $financial_year = auth()->user()->financial_year;
        $candidate_id = $request->candidate_id;
        $fee_group_id = $request->fee_group_id;
        $fine = $request->fine;
        switch ($payment) {
            case 'CreditCard':
                // payment
                $ecom_ConsumerOrderID =  $request->Ecom_ConsumerOrderID;
                $amount =  $request->Lite_Order_Amount;
                $amount =    $amount / 100;
                $status = 0;
                if ($request->Lite_Payment_Card_Status == "0") {
                    FeeCandidateHistory::firstOrCreate([
                        'candidate_id' => $candidate_id,
                        'reference_no' => $ecom_ConsumerOrderID,
                        'amount' =>    $amount,
                        'fee_group_id' => $fee_group_id,
                    ], [
                        'candidate_id' => $candidate_id,
                        'reference_no' =>  $ecom_ConsumerOrderID,
                        'amount' =>    $amount,
                        'fine' =>  $fine,
                        'fee_group_id' => $fee_group_id,
                        'attachment' => '',
                        'pay_via' => 2,
                        'collect_by' => 'online',
                        'remarks' => "Lite :  $ecom_ConsumerOrderID  Candidate number:  $candidateNo ",
                        'status' => 1
                    ]);
                    $status = 1;
                    // Send  Email
                    $this->sendConfirmation($national_id, $candidateNo, $session,  $financial_year);
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
                        FeeCandidateHistory::firstOrCreate([
                            'candidate_id' => $candidate_id,
                            'reference_no' => $thirdPartyConversationID,
                            'amount' =>    $amount,
                            'fee_group_id' => $fee_group_id,
                        ], [
                            'candidate_id' => $candidate_id,
                            'reference_no' => $thirdPartyConversationID,
                            'amount' =>    $amount,
                            'fine' =>  $fine,
                            'fee_group_id' => $fee_group_id,
                            'attachment' => '',
                            'pay_via' => 1,
                            'collect_by' => 'online',
                            'remarks' => "MPESA :  $request->mpesa_mobile  Candidate number:  $candidateNo ",
                            'status' => 1
                        ]);
                        $status = 1;
                        // Send  Email
                        $this->sendConfirmation($national_id, $candidateNo, $session,  $financial_year);
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
            case 'EcoCash':
                // payment
                $validator = Validator::make($request->all(), [
                    'ecocash_mobile' => 'required|digits:8',
                ]);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()]);
                }
                ob_start();
                $ecoCashApi = new EcoCashApi();
                $ecoCashApi =   $ecoCashApi->getEcoCashResponse($request->ecocash_mobile,  $request->total_amount, $candidateNo, 'Exams Fees');
                ob_end_clean();
                $status = 0;
                if ($ecoCashApi) {
                    // convert json to array
                    if (!isset($ecoCashApi->txnstatus) && isset($ecoCashApi->extra_data) &&  isset($ecoCashApi->request_id)) {
                        $thirdPartyConversationID =   $ecoCashApi->request_id;
                        $amount =  $request->total_amount;
                        FeeCandidateHistory::firstOrCreate([
                            'candidate_id' => $candidate_id,
                            'reference_no' => $thirdPartyConversationID,
                            'amount' =>    $amount,
                            'fee_group_id' => $fee_group_id,
                        ], [
                            'candidate_id' => $candidate_id,
                            'reference_no' => $thirdPartyConversationID,
                            'amount' =>    $amount,
                            'fine' =>  $fine,
                            'fee_group_id' => $fee_group_id,
                            'attachment' => '',
                            'pay_via' => 3,
                            'collect_by' => 'online',
                            'remarks' => "EcoCash :  $request->ecocash_mobile  Candidate number:  $candidateNo ",
                            'status' => 1
                        ]);
                        $status = 1;
                        // Send  Email
                        $this->sendConfirmation($national_id, $candidateNo, $session,  $financial_year);
                        $info = [
                            'centreNo' => $request->center_no,
                            'candidate_no' => $candidateNo,
                            'level' => $level,
                            'session' => $session,
                        ];
                        exit(json_encode(['status' => $status, 'message' => $ecoCashApi->message, 'output' =>  $info, 'publised' => is_publised($level, $session)]));
                    } else {
                        return response()->json(['errors' => ['ecocash_mobile' =>  array($ecoCashApi->message)]]);
                    }
                } else {
                    return response()->json(['errors' => ['ecocash_mobile' => array('Transaction Failed')]]);
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




    public function printReceipt($id)
    {


        $candidate =  DB::table('candidate_subject')
            ->select(
                'centers.center_no',
                'centers.center_name',
                'center_candidate.id',
                'center_candidate.candidate_no',
                'center_candidate.national_id',
                'center_candidate.session',
                'center_candidate.level',
                'center_candidate.financial_year',
                'center_candidate.type',
                'center_candidate.subject_number',
                'candidates.candidate_surname',
                'candidates.candidate_other_name',
                'candidates.date_of_birth',
                'candidates.gender',
                DB::raw("COALESCE( (SELECT SUM(fee_candidate_histories.amount) FROM fee_candidate_histories  WHERE
            fee_candidate_histories.candidate_id = center_candidate.id
            ),0) AS  amount_paid"),
                DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code)
                        order by candidate_subject.subject_code separator ',') as subjects"),
                'center_candidate.sponser',
            )
            ->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('center_candidate', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
            })
            ->join('centers', 'centers.center_no', '=', 'center_candidate.center_no')
            ->groupBy('center_candidate.candidate_no')
            ->where('center_candidate.id', '=', $id)
            ->first();




        $subjects = explode(",", $candidate->subjects);
        array_push($subjects, "-");
        $sub_total = 0;
        $total_fine = 0;

        $fee_group_details = DB::table('fee_groups')
            ->select(
                'fee_groups.id',
                'fee_types.fee_name',
                'fee_group_details.subject_code',
                'fee_group_details.amount'
            )
            ->join('fee_group_details', 'fee_group_details.fee_group_id', '=', 'fee_groups.id')
            ->join('fee_types', 'fee_types.id', '=', 'fee_group_details.fee_type_id')
            ->join('sessions', 'sessions.id', '=', 'fee_groups.session_id')
            ->join('levels', 'levels.id', '=', 'fee_groups.level_id')
            ->where('sessions.session', '=', $candidate->session)
            ->where('sessions.financial_year', '=', $candidate->financial_year)
            ->where('fee_groups.candidate_type', '=',   $candidate->type == 2 ? 3 : $candidate->type)
            ->where('levels.level', '=', $candidate->level)
            ->whereIn('fee_group_details.subject_code',  $subjects)
            ->get();



        ob_start();
        $pdf = new exFPDF('P', 'mm', 'A5');
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $table = new easyTable($pdf, '%{30,30, 20, 20}', 'width:70; border:0; font-size:8; line-height:1.2; paddingX:0');
        $table->easyCell('RECEIPT', 'colspan:4;font-style:B; font-size:20;line-height:0.6;');
        $table->printRow();

        $table->easyCell("To  $candidate->candidate_other_name  $candidate->candidate_surname", 'colspan:4');
        $table->printRow();
        $table->rowStyle('min-height:1.8;paddingY:0.02;');
        $table->easyCell('', 'colspan:4; bgcolor:#000;');
        $table->printRow();

        $table->easyCell('Amount Per Serving', 'colspan:4; border:B; border-color:#a1a1a1;');
        $table->printRow();

        $table->easyCell('Calories 200', 'colspan:1; font-style:B;');
        $table->easyCell('Calories from Fat 130', 'colspan:3; align:R');
        $table->printRow();

        $table->rowStyle('min-height:1;paddingY:0.02;');
        $table->easyCell('', 'colspan:4; bgcolor:#000;');
        $table->printRow();

        $table->easyCell('% Daily Value*', 'colspan:4; align:R; font-style:B; border:B;border-color:#a1a1a1;');
        $table->printRow();

        $table->rowStyle('font-style:B; border:B;border-color:#a1a1a1;');
        $table->easyCell('Description', 'colspan:2;');
        $table->printRow();

        foreach ($fee_group_details as  $fee) {
            $sub_total += $fee->amount;
            $fee_name = strtoupper($fee->fee_name);
            $table->rowStyle('border:B;border-color:#a1a1a1;');
            $table->easyCell(" $fee_name", 'colspan:2; paddingX:5;');
            $table->easyCell("$fee->subject_code ", 'colspan:1; paddingX:1;');
            $table->easyCell("LSL" . number_format($fee->amount, 2, '.', ''), 'colspan:2; align:R;font-style:B;');
            $table->printRow();
        }

        $groupId = $fee_group_details->first()->id;
        $fine = FeeFine::where('fee_group_id', '=', $groupId)
            ->where('start_date', '<=',   date('Y-m-d'))
            ->where('end_date', '>=',   date('Y-m-d'))
            ->first();
        if ($fine) {
            $total_fine = $fine->fine_value;
        }
        $total =  $total_fine + $sub_total;



        $table->rowStyle('font-style:B;border:B;border-color:#a1a1a1;');
        $table->easyCell('Sub-Total', 'colspan:2; ');
        $table->easyCell('18%', 'colspan:2; align:R;');
        $table->printRow();

        $table->rowStyle('font-style:B;border:B;border-color:#a1a1a1;');
        $table->easyCell('Fine', 'colspan:2;');
        $table->easyCell('2%', 'colspan:2; align:R;');
        $table->printRow();

        $table->rowStyle('font-style:B;border:B;border-color:#a1a1a1;');
        $table->easyCell('Total', 'colspan:2;');
        $table->easyCell('6%', 'colspan:2; align:R;');
        $table->printRow();

        $table->rowStyle('min-height:1.8;paddingY:0.02;');
        $table->easyCell('', 'colspan:4; bgcolor:#000;');
        $table->printRow();


        ob_end_flush();
        $pdf->Output('D', "Timetable" . ''  . ".pdf");
        exit;
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
                    'fee_candidate_histories.amount',

                ],
            )->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('center_candidate', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
                $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
            })
            ->leftJoin('fee_candidate_histories', function ($join) {
                $join->on('center_candidate.id', '=', 'fee_candidate_histories.candidate_id');
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
