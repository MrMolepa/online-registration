<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libraries\Payment\Payment;
use App\Models\Center;
use App\Models\CenterCandidate;
use App\Models\FeeCandidateHistory;
use App\Models\FeeFine;
use App\Models\FeeGroup;
use App\Models\FeePaymentHistory;
use App\Models\FeePaymentMethod;
use App\Models\FeeType;
use App\Models\Session;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Echo_;
use Yajra\DataTables\Facades\DataTables;

class FeeCandidateHistoryController extends Controller
{

    public function index(Request $request)
    {
        // ini_set('memory_limit', '-1');
        // set_time_limit(-1);
        if ($request->ajax()) {
            $sponsor = $request->sponsor;
            $year = $request->year;
            $center = $request->center;
            $level = $request->level;
            $session = $request->session;
            if ($request->has('filters')) {
                $levels = DB::table('center_candidate')->select(
                    [
                        'center_candidate.level'
                    ],
                );
                // Get Sessions
                $sessions = DB::table('center_candidate')->select(
                    [
                        'center_candidate.session'
                    ],
                );
                // Get Centers
                $centers = DB::table('center_candidate')
                    ->select(
                        'centers.level',
                        'centers.center_name',
                        'center_candidate.center_no'
                    )->join('centers', 'center_candidate.center_no', '=', 'centers.center_no');
                // Get Sponsors
                $sponsors = DB::table('center_candidate')->select(
                    [
                        'center_candidate.sponser'
                    ],
                );
                if (isset($year)) {
                    $levels =  $levels->where('center_candidate.financial_year', "=", $year);
                    // Get Sessions
                    $sessions = $sessions->where('center_candidate.financial_year', "=", $year);
                    // Get Sponsors
                    $sponsors = $sponsors->where('center_candidate.financial_year', "=", $year);
                    // Get Centers
                    $centers = $centers->where('center_candidate.financial_year', "=", $year);
                    // Get Sponsors
                    $sponsors = $sponsors->where('center_candidate.financial_year', "=", $year);
                }
                if (isset($level)) {
                    $levels =  $levels->where('center_candidate.level', "=", $level);
                    // Get Sessions
                    $sessions = $sessions->where('center_candidate.level', "=", $level);
                    // Get Centers
                    $centers = $centers->where('center_candidate.level', "=", $level);
                    // Get Sponsors
                    $sponsors = $sponsors->where('center_candidate.level', "=", $level);
                }
                if (isset($session)) {
                    // Get levels
                    $levels = $levels->where('center_candidate.session', "=", $session);
                    // Get Sessions
                    $sessions = $sessions->where('center_candidate.session', "=", $session);
                    // Get Centers
                    $centers = $centers->where('center_candidate.session', "=", $session);
                    // Get Sponsors
                    $sponsors = $sponsors->where('center_candidate.session', "=", $session);
                }
                if (isset($center)) {
                    $levels =  $levels->where('center_candidate.center_no', "=", $center);
                    // Get Sessions
                    $sessions = $sessions->where('center_candidate.center_no', "=", $center);
                    // Get Centers
                    $centers = $centers->where('center_candidate.center_no', "=", $center);
                    // Get Sponsors
                    $sponsors = $sponsors->where('center_candidate.center_no', "=", $center);
                }
                $levels = $levels->distinct()
                    ->orderBy('level')
                    ->get()->pluck('level')->toArray();
                // Get Sessions
                $sessions = $sessions->distinct()
                    ->orderBy('session')
                    ->get()->pluck('session')->toArray();
                // Get Centers
                $centers = $centers->orderBy('center_candidate.center_no', 'ASC')
                    ->distinct()
                    ->groupBy(['center_candidate.center_no'])
                    ->get()->pluck('center_name', 'center_no')->toArray();
                // Get Sponsors
                $sponsors =  $sponsors->distinct()
                    ->orderBy('sponser')
                    ->get()->pluck('sponser')->toArray();
                return response()->json(['levels' =>  $levels, 'sessions' => $sessions, 'centers' => $centers, 'sponsors' => $sponsors]);
            }

            if ($request->has('payment_history')) {
                $payment_history = FeeCandidateHistory::with('feegroup')
                    ->where('candidate_id', '=', $request->candidate_id);
                return DataTables::eloquent($payment_history)
                    ->addColumn('amount', function ($row) {
                        return  "LSL" . number_format($row->amount, 2, ".", "");
                    })
                    ->addColumn('fine', function ($row) {
                        return   "LSL" . number_format($row->fine, 2, ".", "");
                    })
                    ->addColumn('created_at', function ($row) {
                        return  date('Y M d H:i:s', strtotime($row->created_at));
                    })
                    ->toJson();
            }

            $candidates = DB::table('centers')
                ->select(
                    'centers.center_no',
                    'centers.center_name',
                    'candidates.candidate_no',
                    'center_candidate.national_id',
                    'candidates.candidate_other_name',
                    'candidates.candidate_surname',
                    'candidates.gender',
                    'candidates.date_of_birth',
                    'center_candidate.id',
                    'center_candidate.level',
                    'center_candidate.financial_year',
                    'center_candidate.session',
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
                         )),0) as price")
                )
                ->join('center_candidate', 'center_candidate.center_no', '=', 'centers.center_no')
                ->join('candidates', 'candidates.candidate_no', '=', 'center_candidate.candidate_no');

            if (!empty($center)) {
                $candidates = $candidates->where('centers.center_no', "=", $center);
            }
            if (!empty($year)) {
                $candidates = $candidates->where('center_candidate.financial_year', "=", $year);
            }
            if (!empty($level)) {
                $candidates = $candidates->where('center_candidate.level', "=", $level);
            }
            if (!empty($sponsor)) {
                $candidates = $candidates->where('center_candidate.sponser', "=", $sponsor);
            }
            if (!empty($session)) {
                $candidates = $candidates->where('center_candidate.session', "=", $session);
            }
            $candidates = $candidates
            ->orderBy('center_candidate.candidate_no', 'asc');
            return DataTables::of($candidates)
                ->setRowId('candidate_no')
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('admin.fees-stracture.fee-histories.show', $row->id)  . '" data-original-title="collect" class="view-history btn btn-primary"><i class="fa fa-arrow-right" aria-hidden="true">Collect</i></a>';
                    return $btn;
                })
                ->setRowId('candidate_no')
                ->editColumn('candidate_no', function ($row) {
                    return   str_pad($row->candidate_no, 9, '0', STR_PAD_LEFT);
                })
                ->editColumn('national_id', function ($row) {
                    return   str_pad($row->national_id, 12, '0', STR_PAD_LEFT);
                })
                ->editColumn('actions', function ($row) {
                    $url = route('sponsor.candidate.edit', $row->id);
                    return  "<a class='btn  bg-gradient-primary btn-sm approval_btn' data-action='$url' href='javascript:void(0)'>Action</a>";
                })
                ->editColumn('amount_paid', function ($row) {
                    return   "LSL " . number_format($row->amount_paid, 2, '.', '');
                })
                ->editColumn('date_of_birth', function ($row) {
                    return $row->date_of_birth;
                })
                ->rawColumns(['candidate_no', 'national_id', 'action'])
                ->make();
        }
        $years =  CenterCandidate::select(DB::raw('financial_year as year'))
            ->orderBy('year', 'DESC')
            ->distinct()
            ->get()->pluck('year');
        $feegroups = FeeGroup::get();
        $feepaymentmethods = FeePaymentMethod::get();
        return view('admin.finance.fee-candidate-history.index', compact('years', 'feegroups', 'feepaymentmethods'));
    }



    public function show($id)
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
        $feeoutputHTML = '';
        $feeoutputHTML  .= ' <table style="width: 100%; table-layout: fixed">
              <thead>
                <tr>
                  <th style="text-align: left; padding-left: 0;">Description</th>
                  <th style="text-align: center;">Subject Code</th>
                  <th style="text-align: right; padding-right: 0;">Amount</th>
                </tr>
              </thead>
              <tbody>
                <tr class="invoice-items">';

        foreach ($fee_group_details as  $fee) {
            $sub_total += $fee->amount;
            $feeoutputHTML .= "</tr>
                  <tr class='invoice-items'>
                    <td style='text-align: left;'>$fee->fee_name</td>
                    <td  style='text-align: center;'>$fee->subject_code</td>
                    <td style='text-align: right;'> LSL" . number_format($fee->amount, 2, '.', '') . "</td>
                  </tr>";
        }
        $feeoutputHTML  .= '</tr>
              </tbody>
            </table>';
        $groupId = $fee_group_details->first()->id;
        $fine = FeeFine::where('fee_group_id', '=', $groupId)
            ->where('start_date', '<=',   date('Y-m-d'))
            ->where('end_date', '>=',   date('Y-m-d'))
            ->first();
        if ($fine) {
            $total_fine = $fine->fine_value;
        }
        $total =  $total_fine + $sub_total;
        $balance =  $total - $candidate->amount_paid;
        $url = route('admin.fees-stracture.fee-histories.update', $id);
        return response()->json([
            'candidate' => $candidate,
            'url' => $url,
            'fine' => $fine,
            'total' =>  $total,
            'sub_total' => $sub_total,
            'total_fine' => $total_fine,
            'balance' => $balance,
            'html' => $feeoutputHTML,
            'groupId' => $groupId
        ]);
    }
    public function update(Request $request, $id)
    {
        //
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'reference_no' => 'required|string',
            'amount'  => 'required|string',
            'fine'  => 'required|string',
            'fee_group_id'  => 'required|string',
            'attachment'  => 'required',
            'pay_via' => 'required|string',
            'remarks'  => 'required|string',
            'status'  => 'required|string',
        ]);
        //document_meta_datas
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $attactment = $request->file('attachment')->storeAs('attachments', time() . '.' . $request->file('attachment')->getClientOriginalExtension(),  'local');
        if ($attactment == null ||    $attactment == '') {
            return response()->json(['attachment' => ['Error in storing document in local']]);
        }
        $CandidateFee = new FeePaymentHistory();
        $CandidateFee->candidate_id = $request->candidate_id;
        $CandidateFee->reference_no = $request->reference_no;
        $CandidateFee->amount = $request->amount;
        $CandidateFee->fine = $request->fine;
        $CandidateFee->fee_group_id = $request->fee_group_id;
        $CandidateFee->attachment = $attactment;
        $CandidateFee->pay_via = $request->pay_via;
        $CandidateFee->collect_by = Auth::user()->email;
        $CandidateFee->remarks = $request->remarks;
        $CandidateFee->status = $request->status;
        $CandidateFee->save();
        return response()->json(['success' => "Payment History successfully added"]);
    }
}
