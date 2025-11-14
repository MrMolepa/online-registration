<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Libraries\Mpesa\MpesaApi;
use Illuminate\Http\Request;
use  App\Libraries\Payment\Payment;
use App\Models\Candidate;
use App\Models\Center;
use App\Models\CenterPayment;
use App\Models\FeeCandidateHistory;
use App\Models\FeeFine;
use App\Models\Level;
use App\Models\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PaymentController extends Controller
{
    public function index(Request $request)
    {

        $center_no = auth()->user()->center_no;
        $center = Center::where('center_no', '=', $center_no)->first();
        $centerSessions = json_decode($center->sessions, true);

        $date = date('Y-m-d');
        $session = Session::where('financial_closing_date', '>=',  $date)
            ->whereIn('session', $centerSessions)->first();


        if ($request->ajax()) {

            $centerPayments   =  CenterPayment::where('center_no', '=', $center_no)
                ->where('financial_year', '=',  $session->financial_year)
                ->where('session', '=',  $session->session)
                ->get();
            return Datatables::of($centerPayments)
                ->addColumn('checked_status', function ($model) {
                    $checkeStatus = $model->status;
                    $textColor = "";
                    $status = " ";

                    if ($checkeStatus == 0) {
                        $textColor =  'text-warning';
                        $status =  'Pending';
                    } elseif ($checkeStatus == 2) {
                        $textColor =  'text-success';
                        $status =  'Checked';
                    } else {
                        $textColor =  'text-danger';
                        $status =  'Invalid';
                    }
                    return "<div class='action-label'>
                                <a class='btn btn-white btn-sm btn-rounded' href='javascript:void(0);'>
                                    <i class='fas fa-dot-circle $textColor'></i>
                                    $status
                                </a>
                            </div>";
                })
                ->addColumn('updated_at', function ($model) {
                    return $model->updated_at;
                })
                ->addColumn('amount_paid', function ($model) {
                    $amount = 'LSL ' . number_format($model->amount, 2, '.', '');
                    return $amount;
                })
                ->rawColumns(['checked_status', 'updated_at', 'amount_paid'])
                ->make(true);
        }
        return view('school.payments', compact('center'));
    }

    public function candidates(Request $request)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(-1);
        $center_no = auth()->user()->center_no;
        $center = Center::where('center_no', '=', $center_no)->first();
        $centerSessions = json_decode($center->sessions, true);
        $date = date('Y-m-d');
        $session = Session::where('financial_closing_date', '>=',  $date)
            ->whereIn('session', $centerSessions)->first();
        if ($request->ajax()) {


            $candidates_array = array();
            //GRAND TOTAL
            $grand_total = 0;
            $candidate_profile = 0;
            $guardian_profile = 0;
            DB::table('candidate_subject')
                ->select(
                    'center_candidate.center_no',
                    'center_candidate.id',
                    'center_candidate.candidate_no',
                    'center_candidate.national_id',
                    'center_candidate.type',
                    'center_candidate.subject_number',
                    'center_candidate.session',
                    'center_candidate.financial_year',
                    'center_candidate.level',
                    'candidates.candidate_surname',
                    'candidates.candidate_other_name',
                    'candidates.date_of_birth',
                    'candidates.gender',
                    'guardians.surname',
                    'addresses.user_id',
                    DB::raw("COALESCE( (SELECT SUM(fee_candidate_histories.amount) FROM fee_candidate_histories  WHERE
                fee_candidate_histories.candidate_id = center_candidate.id
                and fee_candidate_histories.status='1'
                ),0) AS amount_paid"),
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
                    DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code)
                order by candidate_subject.subject_code separator ',') as subjects"),
                    'center_candidate.sponser',
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
                ->leftJoin('guardians', function ($join) {
                    $join->on('candidate_subject.candidate_no', '=', 'guardians.candidate');
                })
                ->leftJoin('addresses', function ($join) {
                    $join->on('candidate_subject.national_id', '=', 'addresses.user_id');
                    $join->where('addresses.user_type', '=', Candidate::class);
                })
                ->groupBy('center_candidate.candidate_no')
                ->where('center_candidate.center_no', '=', $center_no)
                ->where('center_candidate.level', '=', $center->level)
                ->where('center_candidate.financial_year', '=', $session->financial_year)
                ->where('center_candidate.session', '=', $session->session)
                ->where('center_candidate.sponser', '=', "O")
                ->whereNull('fee_candidate_histories.amount')
                ->orderBy('candidates.candidate_no', "ASC")
                ->each(function (object $candidate) use (
                    &$candidates_array,
                    &$grand_total,
                    &$candidate_profile,
                    &$guardian_profile,
                ) {

                    $candidate_information = array();
                    $total_amount = $candidate->exam_fee;
                    $groupId = $candidate->fee_group_id;
                    $fee_fine = FeeFine::where('fee_group_id', '=', $groupId)
                        ->where('start_date', '<=',   date('Y-m-d'))
                        ->where('end_date', '>=',   date('Y-m-d'))
                        ->first();
                    if ($fee_fine !== null) {
                        $total_amount += $fee_fine->fine_value;
                        $fine = $fee_fine->fine_value;
                    }

                    $candidate_information['candidate_no'] = $candidate->candidate_no;
                    $candidate_information['candidate_surname'] = $candidate->candidate_surname;
                    $candidate_information['candidate_other_name'] = $candidate->candidate_other_name;
                    $candidate_information['guardian_profile'] = is_null($candidate->surname) ? "" : $candidate->surname;
                    $candidate_information['candidate_profile'] = is_null($candidate->user_id) ? "" : $candidate->user_id;
                    $candidate_information['national_id'] = $candidate->national_id;
                    $candidate_information['exam_fee'] = intval($total_amount);
                    $candidates_array[] = $candidate_information;
                    if (is_null($candidate->surname) || is_null($candidate->user_id)) {
                        $candidate_profile = 1;
                        $guardian_profile =  1;
                    }
                    $grand_total += $total_amount;
                });

            return response()->json(['candidates' => $candidates_array, 'grand_total' => $grand_total, 'candidate_profile' => $candidate_profile, 'guardian_profile' => $guardian_profile]);
        }
    }

    private function getCandidateFees($candidate_numbers)
    {

        $center_no = auth()->user()->center_no;
        $center = Center::where('center_no', '=', $center_no)->first();
        $centerSessions = json_decode($center->sessions, true);
        $date = date('Y-m-d');
        $session = Session::where('financial_closing_date', '>=',  $date)
            ->whereIn('session', $centerSessions)->first();
        $candidates_array = array();
        $candidates = DB::table('candidate_subject')
            ->select(
                'center_candidate.center_no',
                'center_candidate.id',
                'center_candidate.candidate_no',
                'center_candidate.national_id',
                'center_candidate.type',
                'center_candidate.subject_number',
                'center_candidate.session',
                'center_candidate.financial_year',
                'center_candidate.level',
                'candidates.candidate_surname',
                'candidates.candidate_other_name',
                'candidates.date_of_birth',
                'candidates.gender',
                'guardians.surname',
                'addresses.user_id',
                DB::raw("COALESCE( (SELECT SUM(fee_candidate_histories.amount) FROM fee_candidate_histories  WHERE
        fee_candidate_histories.candidate_id = center_candidate.id
        and fee_candidate_histories.status='1'
        ),0) AS amount_paid"),
                DB::raw("(Select fee_groups.id from fee_groups
                inner join fee_group_details on  fee_group_details.fee_group_id=fee_groups.id
                inner join fee_types on  fee_types.id=fee_group_details.fee_type_id
                inner join sessions on  sessions.id=fee_groups.session_id
                inner join levels on  levels.id=fee_groups.level_id
                where sessions.session=center_candidate.session and
                sessions.financial_year=center_candidate.financial_year and
                levels.level=center_candidate.level and
                fee_groups.candidate_type= CASE `center_candidate`.`type`
                                            WHEN 1 THEN 1
 	                        WHEN 2 THEN 3
 	                        ELSE 3
                             END
                   LIMIT 1
                             ) as fee_group_id "),

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
                DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code)
        order by candidate_subject.subject_code separator ',') as subjects"),
                'center_candidate.sponser',
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
            ->leftJoin('guardians', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'guardians.candidate');
            })
            ->leftJoin('addresses', function ($join) {
                $join->on('candidate_subject.national_id', '=', 'addresses.user_id');
                $join->where('addresses.user_type', '=', Candidate::class);
            })
            ->groupBy('center_candidate.candidate_no')
            ->where('center_candidate.center_no', '=', $center_no)
            ->where('center_candidate.level', '=', $center->level)
            ->where('center_candidate.financial_year', '=', $session->financial_year)
            ->where('center_candidate.session', '=', $session->session)
            ->where('center_candidate.sponser', '=', "O")
            ->whereNull('fee_candidate_histories.amount')
            ->whereNotNull('guardians.candidate');
        if (sizeof($candidate_numbers) != 0) {
            $candidates =  $candidates->whereIn('center_candidate.candidate_no', $candidate_numbers);
        }

        $candidates = $candidates->whereNotNull('addresses.user_id')
            ->orderBy('candidates.candidate_no', "ASC")
            ->each(function (object $candidate) use (
                &$candidates_array,
            ) {
                $candidate_information = array();
                $total_amount = $candidate->exam_fee;
                $fine = 0;
                $groupId = $candidate->fee_group_id;
                $fee_fine = FeeFine::where('fee_group_id', '=', $groupId)
                    ->where('start_date', '<=',   date('Y-m-d'))
                    ->where('end_date', '>=',   date('Y-m-d'))
                    ->first();
                if ($fee_fine !== null) {
                    $total_amount += $fee_fine->fine_value;
                    $fine = $fee_fine->fine_value;
                }
                $candidate_information['fee_group_id'] = $candidate->fee_group_id;
                $candidate_information['candidate_id'] = $candidate->id;
                $candidate_information['candidate_no'] = $candidate->candidate_no;
                $candidate_information['candidate_surname'] = $candidate->candidate_surname;
                $candidate_information['candidate_other_name'] = $candidate->candidate_other_name;
                $candidate_information['session'] = $candidate->session;
                $candidate_information['financial_year'] = $candidate->financial_year;
                $candidate_information['level'] = $candidate->level;
                $candidate_information['exam_fee'] = $total_amount;
                $candidate_information['fine'] = $fine;
                $candidates_array[] = (object)$candidate_information;
            });
        return collect($candidates_array);
    }
    public function payment()
    {
        $center_no = auth()->user()->center_no;
        $center = Center::where('center_no', '=', $center_no)->first();
        $centerSessions = json_decode($center->sessions, true);
        $date = date('Y-m-d');
        $session = Session::where('financial_closing_date', '>=',  $date)
            ->whereIn('session', $centerSessions)->first();
        return response()->json(Payment::schoolfees($center_no, $center->level,  $session->session, $session->financial_year));
    }

    public function makePayement(Request $request)
    {

        $payment = $request->payment;
        $center_no = auth()->user()->center_no;
        $center = Center::where('center_no', '=', $center_no)->first();
        $centerSessions = json_decode($center->sessions, true);
        $date = date('Y-m-d');
        $session = Session::where('financial_closing_date', '>=',  $date)
            ->whereIn('session', $centerSessions)->first();
        $candidate_numbers = array_filter($request->candidate_no, function ($value) {
            return !is_null($value) && $value !== '';
        });
        switch ($payment) {
            case 'CashDeposit':
                // payment
                $validator = Validator::make($request->all(), [
                    'bank_statement' => 'required',
                    'bank_statement.*' => 'required',
                ]);
                if ($validator->passes()) {
                    if ($request->file()) {
                        $candidates = $this->getCandidateFees($candidate_numbers);
                        foreach ($request->file('bank_statement') as $key => $file) {

                            $centerPayment = new CenterPayment();
                            $centerPayment->center_no =  $center_no;
                            $centerPayment->amount = 0;
                            $centerPayment->reference_no = '';
                            $fileName = $center_no . '-' . time() . '-' . $file->getClientOriginalName();
                            $filePath = $file->storeAs('bankStatement/' . $center_no, $fileName, 'public');
                            $centerPayment->attachment = '/storage/' .  $filePath;
                            $centerPayment->financial_year =  $session->financial_year;
                            $centerPayment->session =  $session->session;
                            $centerPayment->status = 0;
                            $centerPayment->remarks = json_encode($candidates);
                            $centerPayment->collect_by = '';
                            $centerPayment->save();
                        }
                        $fileNumber = count($request->file('bank_statement'));
                        return response()->json(['success' => "Successfully uploaded $fileNumber files"]);
                    }
                }
                return response()->json(['error' => $validator->errors()]);
                break;
            case 'CreditCard':
                // payment
                $cardStatus = $request->Lite_Payment_Card_Status;
                $ecom_ConsumerOrderID = $request->Ecom_ConsumerOrderID;
                $amount = $request->Lite_Order_Amount;
                $amount =    $amount / 100;
                $status = 0;
                if ($cardStatus == "0") {
                    $candidates = $this->getCandidateFees($candidate_numbers);
                    foreach ($candidates  as $candidate) {
                        FeeCandidateHistory::firstOrCreate([
                            'candidate_id' => $candidate->candidate_id,
                            'reference_no' =>  $ecom_ConsumerOrderID,
                            'amount' =>  $candidate->exam_fee,
                            'fee_group_id' => $candidate->fee_group_id,
                        ], [
                            'candidate_id' => $candidate->candidate_id,
                            'reference_no' => $ecom_ConsumerOrderID,
                            'amount' =>  $candidate->exam_fee,
                            'fine' =>  $candidate->fine,
                            'fee_group_id' => $candidate->fee_group_id,
                            'attachment' => '',
                            'pay_via' => 2,
                            'collect_by' => 'online',
                            'remarks' => "LITE: $request->mpesa_mobile  Candidate number:$candidate->candidate_no",
                            'status' => 1
                        ]);
                    }
                    $status = 1;
                }
                return response()->json(['status' => $status]);
            case 'VclMpesa':
                $validator = Validator::make($request->all(), [
                    'mpesa_mobile' => 'required|digits:8'
                ]);
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()]);
                }
                ob_start();
                $mpesa = new  MpesaApi();
                $mpesa_api = $mpesa->C2BMpesa($request->mpesa_mobile, (int)$request->amount);
                $status = 0;
                ob_end_clean();
                if (!is_null($mpesa_api)) {
                    // convert json to array
                    $mpesa_body = json_decode($mpesa_api->body, true);
                    if ($mpesa_body['output_ResponseCode'] == 'INS-0') {
                        // Register Candidate
                        $status = 1;
                        $thirdPartyConversationID =   $mpesa_body['output_ThirdPartyConversationID'];
                        $amount =  $request->total_amount;
                        $candidates = $this->getCandidateFees($candidate_numbers);
                        foreach ($candidates  as $candidate) {
                            FeeCandidateHistory::firstOrCreate([
                                'candidate_id' => $candidate->candidate_id,
                                'reference_no' =>  $thirdPartyConversationID,
                                'amount' =>  $candidate->exam_fee,
                                'fee_group_id' => $candidate->fee_group_id,
                            ], [
                                'candidate_id' => $candidate->candidate_id,
                                'reference_no' =>  $thirdPartyConversationID,
                                'amount' =>  $candidate->exam_fee,
                                'fine' =>  $candidate->fine,
                                'fee_group_id' => $candidate->fee_group_id,
                                'attachment' => '',
                                'pay_via' => 1,
                                'collect_by' => 'online',
                                'remarks' => "MPESA:$request->mpesa_mobile  Candidate number:$candidate->candidate_no",
                                'status' => 1
                            ]);
                        }
                        return response()->json(['status' => $status]);
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

    public function uploadBankStament(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_statement' => 'required',
            'bank_statement.*' => 'required',
        ]);
        if ($validator->passes()) {
            $center_no = auth()->user()->center_no;
            $center = Center::where('center_no', '=', $center_no)->first();
            $centerSessions = json_decode($center->sessions, true);
            $date = date('Y-m-d');
            $session = Session::where('financial_closing_date', '>=',  $date)
                ->whereIn('session', $centerSessions)->first();
            if ($request->file()) {
                foreach ($request->file('bank_statement') as $key => $file) {
                    $centerPayment = new CenterPayment();
                    $centerPayment->center_no =  $center_no;
                    $centerPayment->amount = 0;
                    $centerPayment->reference_no = '';
                    $fileName = $center_no . '-' . time() . '-' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('bankStatement/' . $center_no, $fileName, 'public');
                    $centerPayment->attachment = '/storage/' .  $filePath;
                    $centerPayment->financial_year =  $session->financial_year;
                    $centerPayment->session =  $session->session;
                    $centerPayment->status = 0;
                    $centerPayment->remarks = '';
                    $centerPayment->collect_by = '';
                    $centerPayment->save();
                }
                $fileNumber = count($request->file('bank_statement'));
                return response()->json(['success' => "Successfully uploaded $fileNumber files"]);
            }
        }
        return response()->json(['error' => $validator->errors()]);
    }
}
