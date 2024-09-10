<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Libraries\Mpesa\MpesaApi;
use Illuminate\Http\Request;
use  App\Libraries\Payment\Payment;
use App\Models\BankStatement;
use App\Models\Candidate;
use App\Models\Center;
use App\Models\Invoice;
use App\Models\Level;
use App\Models\Payment as PaymentModel;
use App\Models\Session;
use App\Models\Subject;
use Composer\Semver\Interval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use \NumberFormatter;
use Yajra\DataTables\Facades\DataTables;

class PaymentController extends Controller
{
    public function index(Request $request)
    {

        $financial_year = "";
        if (date('m') <= 2) { //Upto June 2014-2015
            $financial_year = (date('Y') - 1) . '-' . date('Y');
        } else { //After June 2015-2016
            $financial_year = date('Y') . '-' . (date('Y') + 1);
        }
        $center_no = auth()->user()->center_no;
        $center = Center::where('center_no', '=', $center_no)->first();
        if ($request->ajax()) {

            $bankStaments  =  BankStatement::where('center_id', '=', $center_no)
                ->where('financial_year', '=', $financial_year)->get();
            return Datatables::of($bankStaments)
                ->addColumn('checked_status', function ($model) {
                    $checkeStatus = $model->checked_status;
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
                    $amount = 'LSL ' . number_format($model->amount_paid, 2, '.', '');
                    return $amount;
                })
                ->rawColumns(['checked_status', 'updated_at', 'amount_paid'])
                ->make(true);
        }
        return view('school.payments', compact('center'));
    }

    public function candidates(Request $request)
    {

        $center_no = auth()->user()->center_no;
        $center = Center::with('subjects')->where('center_no', '=', $center_no)->first();
        $centerSessions = json_decode($center->sessions, true);
        $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        $session = Session::whereIn('session', $centerSessions)->where('financial_year', '=', $financial_year)->first();
        if ($request->ajax()) {
            $candidates = DB::table('candidate_subject')
                ->select(
                    'center_candidate.candidate_no',
                    'center_candidate.national_id',
                    'center_candidate.type',
                    'center_candidate.subject_number',
                    'candidates.candidate_surname',
                    'candidates.candidate_other_name',
                    'candidates.date_of_birth',
                    'center_candidate.sponser',
                    'invoices.amount',
                    'guardians.surname',
                    'addresses.user_id',
                    DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code)
                    order by candidate_subject.subject_code separator ',') as subjects")
                )
                ->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
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
                ->where('center_candidate.sponser', '=', "M")
                ->whereNull('invoices.amount')
                ->orderBy('candidates.candidate_no', "ASC")
                ->get();
            $level = strtolower($center->level);
            $schoolFees =    DB::table('fees_stracture')
                ->where('candidate_type', '=', "$level-school")
                ->where('financial_year', '=',   $session->financial_year)
                ->first();
            $schoolPrivate = DB::table('fees_stracture')
                ->where('candidate_type', '=', "$level-private")
                ->where('financial_year', '=',   $session->financial_year)
                ->first();
            $candidates_array = array();
            //GRAND TOTAL
            $grand_total = 0;
            $candidate_profile = 0;
            $guardian_profile = 0;
            $practicalSubjects = ['0179', '0189', '0191', '0192', '0194', '0417', '0190'];
            $delf = Subject::whereHas('selectedDiscipline', function ($q) {
                $q->where('name', '=', 'LGCSE7');
            })->get()->pluck('subject_code')->toArray();

            foreach ($candidates as $candidate) {
                $candidate_information = array();
                $subjects = explode(",", $candidate->subjects);
                $total_amount = 0;
                if (in_array($candidate->type, [2, 3])) {
                    foreach ($subjects as $subject) {

                        if (in_array($subject, $practicalSubjects)) {
                            $total_amount += $schoolPrivate->subject_fee + $schoolPrivate->practical_subject_fee;
                        } else if (in_array($subject, $delf)) {
                            $total_amount += ($schoolPrivate->delf_fee);
                        } else {
                            $total_amount += $schoolPrivate->subject_fee;
                        }
                    }
                    $total_amount  +=  $schoolPrivate->local_fee + $schoolPrivate->registration_fee;
                    $grand_total += $total_amount;
                } else {
                    foreach ($subjects as $subject) {
                        if (in_array($subject, $practicalSubjects)) {
                            $total_amount += $schoolFees->subject_fee + $schoolFees->practical_subject_fee;
                        } else if (in_array($subject, $delf)) {
                            $total_amount += ($schoolFees->delf_fee);
                        } else {
                            $total_amount += $schoolFees->subject_fee;
                        }
                    }
                    $total_amount  +=  $schoolFees->local_fee + $schoolFees->registration_fee;
                    $grand_total += $total_amount;
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
            }
            return response()->json(['candidates' => $candidates_array, 'grand_total' => $grand_total, 'candidate_profile' => $candidate_profile, 'guardian_profile' => $guardian_profile]);
        }
    }

    private function getCandidateFees($candidate_numbers )
    {
        $center_no = auth()->user()->center_no;
        $center = Center::with('subjects')->where('center_no', '=', $center_no)->first();
        $centerSessions = json_decode($center->sessions, true);
        $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        $session = Session::whereIn('session', $centerSessions)->where('financial_year', '=', $financial_year)->first();
        $candidates = DB::table('candidate_subject')
            ->select(
                'center_candidate.candidate_no',
                'center_candidate.national_id',
                'center_candidate.type',
                'center_candidate.subject_number',
                'candidates.candidate_surname',
                'candidates.candidate_other_name',
                'candidates.date_of_birth',
                'center_candidate.sponser',
                'invoices.amount',
                'guardians.surname',
                'addresses.user_id',
                DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code,'-',candidate_subject.subject_option)
                    order by candidate_subject.subject_code separator ',') as subjects")
            )
            ->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
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
            ->where('center_candidate.session', '=', $session->session);
        if (sizeof($candidate_numbers) != 0) {
            $candidates =  $candidates->whereIn('center_candidate.candidate_no', $candidate_numbers);
        }
        $candidates =  $candidates->where('center_candidate.sponser', '=', "O")
            ->whereNull('invoices.amount')
            ->orderBy('candidates.candidate_no', "ASC")
            ->get();
        $level = strtolower($center->level);
        $schoolFees =    DB::table('fees_stracture')
            ->where('candidate_type', '=', "$level-school")
            ->where('financial_year', '=',   $session->financial_year)
            ->first();
        $schoolPrivate = DB::table('fees_stracture')
            ->where('candidate_type', '=', "$level-private")
            ->where('financial_year', '=',   $session->financial_year)
            ->first();
        $candidates_array = array();
        //GRAND TOTAL
        $grand_total = 0;
        $practicalSubjects = ['0179', '0189', '0191', '0192', '0194', '0417', '0190'];
        $delf = Subject::whereHas('selectedDiscipline', function ($q) {
            $q->where('name', '=', 'LGCSE7');
        })->get()->pluck('subject_code')->toArray();



        foreach ($candidates as $candidate) {
            $candidate_information = array();
            $subjects = explode(",", $candidate->subjects);
            $total_amount = 0;
            if (in_array($candidate->type, [2, 3])) {
                foreach ($subjects as $subject) {
                    if (in_array($subject, $practicalSubjects)) {
                        $total_amount += $schoolPrivate->subject_fee + $schoolPrivate->practical_subject_fee;
                    } else if (in_array($subject, $delf)) {
                        $total_amount += ($schoolPrivate->delf_fee);
                    } else {
                        $total_amount += $schoolPrivate->subject_fee;
                    }
                }
                $total_amount  +=  $schoolPrivate->local_fee + $schoolPrivate->registration_fee;
                $grand_total += $total_amount;
            } else {
                foreach ($subjects as $subject) {
                    if (in_array($subject, $practicalSubjects)) {
                        $total_amount += $schoolFees->subject_fee + $schoolFees->practical_subject_fee;
                    } else if (in_array($subject, $delf)) {
                        $total_amount += ($schoolFees->delf_fee);
                    } else {
                        $total_amount += $schoolFees->subject_fee;
                    }
                }
                $total_amount  +=  $schoolFees->local_fee + $schoolFees->registration_fee;
                $grand_total += $total_amount;
            }
            $candidate_information['candidate_no'] = $candidate->candidate_no;
            $candidate_information['candidate_surname'] = $candidate->candidate_surname;
            $candidate_information['candidate_other_name'] = $candidate->candidate_other_name;
            $candidate_information['guardian_profile'] = is_null($candidate->surname) ? "" : $candidate->surname;
            $candidate_information['candidate_profile'] = is_null($candidate->user_id) ? "" : $candidate->user_id;
            $candidate_information['national_id'] = $candidate->national_id;
            $candidate_information['session'] = $session->session;
            $candidate_information['financial_year'] = $session->financial_year;
            $candidate_information['level'] = $center->level;
            $candidate_information['exam_fee'] = $total_amount;
            $candidates_array[] = (object)$candidate_information;
        }
        return collect($candidates_array);
    }
    public function payment()
    {
        $center_no = auth()->user()->center_no;
        $center = Center::with('subjects')->where('center_no', '=', $center_no)->first();
        $centerSessions = json_decode($center->sessions, true);
        $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        $session = Session::whereIn('session', $centerSessions)->where('financial_year', '=', $financial_year)->first();
        return response()->json(Payment::schoolfees($center_no, $center->level,  $session->session, $session->financial_year));
    }

    public function makePayement(Request $request)
    {
        $center_no = auth()->user()->center_no;
        $payment = $request->payment;
        $center = Center::with('subjects')->where('center_no', '=', $center_no)->first();
        $centerSessions = json_decode($center->sessions, true);
        $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        $session = Session::whereIn('session', $centerSessions)->where('financial_year', '=', $financial_year)->first();
        $level = Level::where('level', '=', $center->level)->first();
        $candidate_numbers=array_filter($request->candidate_no, function($value) { return !is_null($value) && $value !== ''; });

        switch ($payment) {
            case 'CashDeposit':
                // payment
                $validator = Validator::make($request->all(), [
                    'bank_statement' => 'required',
                    'bank_statement.*' => 'required',
                ]);
                if ($validator->passes()) {
                    $center_no = auth()->user()->center_no;
                    $center = Center::with('subjects')->where('center_no', '=', $center_no)->first();
                    $centerSessions = json_decode($center->sessions, true);
                    $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
                    $session = Session::whereIn('session', $centerSessions)->where('financial_year', '=', $financial_year)->first();
                    if ($request->file()) {
                        $candidates = $this->getCandidateFees( $candidate_numbers );
                        foreach ($request->file('bank_statement') as $key => $file) {
                            $bankStament = new BankStatement();
                            $bankStament->email = $request->email;
                            $bankStament->center_id = $center_no;
                            $bankStament->financial_year = $financial_year;
                            $bankStament->candidates =   json_encode($candidates);
                            $fileName = $center_no . '-' . time() . '-' . $file->getClientOriginalName();
                            $filePath = $file->storeAs('bankStatement/' . $center_no, $fileName, 'public');
                            $bankStament->bank_statement = time() . '_' . $file->getClientOriginalName();
                            $bankStament->bank_statement_path = '/storage/' . $filePath;
                            $bankStament->save();
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
                    $candidates = $this->getCandidateFees( $candidate_numbers );
                    foreach ($candidates  as $candidate) {
                        $invoice = Invoice::firstOrCreate([
                            'client_id' => $candidate->candidate_no,
                            'national_id' => $candidate->national_id,
                            'level' => $level->level,
                            'session' => $session->session,
                            'reference_no' =>   $ecom_ConsumerOrderID,
                            'amount' =>  $candidate->exam_fee,
                            'financial_year' => $financial_year,
                        ], [
                            'client_id' => $candidate->candidate_no,
                            'national_id' => $candidate->national_id,
                            'level' => $level->level,
                            'session' => $session->session,
                            'reference_no' =>   $ecom_ConsumerOrderID,
                            'amount' =>  $candidate->exam_fee,
                            'financial_year' => $financial_year,
                        ]);
                        PaymentModel::firstOrCreate(
                            [
                                "invoice_id" =>  $invoice->id,
                                "reference_no" =>   $ecom_ConsumerOrderID,
                                "amount" => $amount
                            ],
                            [
                                "invoice_id" =>  $invoice->id,
                                "reference_no" =>   $ecom_ConsumerOrderID,
                                "amount" => $amount,
                            ]
                        );
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
                        $candidates = $this->getCandidateFees( $candidate_numbers );
                        foreach ($candidates  as $candidate) {
                            $invoice = Invoice::firstOrCreate([
                                'client_id' => $candidate->candidate_no,
                                'national_id' => $candidate->national_id,
                                'level' => $level->level,
                                'session' => $session->session,
                                'reference_no' =>  $thirdPartyConversationID,
                                'amount' =>  $candidate->exam_fee,
                                'financial_year' => $financial_year,
                            ], [
                                'client_id' => $candidate->candidate_no,
                                'national_id' => $candidate->national_id,
                                'level' => $level->level,
                                'session' => $session->session,
                                'reference_no' =>  $thirdPartyConversationID,
                                'amount' =>  $candidate->exam_fee,
                                'financial_year' => $financial_year,
                            ]);
                            PaymentModel::firstOrCreate(
                                [
                                    "invoice_id" =>  $invoice->id,
                                    "reference_no" =>  $thirdPartyConversationID
                                ],
                                [
                                    "invoice_id" =>  $invoice->id,
                                    "reference_no" =>  $thirdPartyConversationID,
                                    "amount" => $amount,
                                ]
                            );
                        }
                    }else {
                        exit(json_encode(['errors' => array('mpesa_mobile' => array($mpesa_body['output_ResponseDesc']))]));
                    }
                }
                else {
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
            $center = Center::with('subjects')->where('center_no', '=', $center_no)->first();
            $centerSessions = json_decode($center->sessions, true);
            $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
            $session = Session::whereIn('session', $centerSessions)->where('financial_year', '=', $financial_year)->first();
            if ($request->file()) {
                foreach ($request->file('bank_statement') as $key => $file) {
                    $bankStament = new BankStatement();
                    $bankStament->email = $request->email;
                    $bankStament->center_id = $center_no;
                    $bankStament->financial_year = $financial_year;
                    $fileName = $center_no . '-' . time() . '-' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('bankStatement/' . $center_no, $fileName, 'public');
                    $bankStament->bank_statement = time() . '_' . $file->getClientOriginalName();
                    $bankStament->bank_statement_path = '/storage/' . $filePath;
                    $bankStament->save();
                }
                $fileNumber = count($request->file('bank_statement'));
                return response()->json(['success' => "Successfully uploaded $fileNumber files"]);
            }
        }
        return response()->json(['error' => $validator->errors()]);
    }
}
