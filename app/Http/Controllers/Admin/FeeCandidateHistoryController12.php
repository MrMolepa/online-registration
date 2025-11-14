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

        ini_set('memory_limit', '-1');
        set_time_limit(-1);
        if ($request->ajax()) {
            // --- Handle filters ---
            if ($request->has('filters')) {
                return $this->getFilters($request);
            }

            // --- Handle payment history ---
            if ($request->has('payment_history')) {
                return $this->getPaymentHistory($request);
            }

            // --- Handle candidates listing ---
            return $this->getCandidates($request);
        }

        // --- Non-AJAX request ---
        $years = CenterCandidate::select('financial_year as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $feegroups = FeeGroup::all();
        $feepaymentmethods = FeePaymentMethod::all();

        return view('admin.finance.fee-candidate-history.index', compact('years', 'feegroups', 'feepaymentmethods'));
    }

    protected function getFilters(Request $request)
    {
        $query = DB::table('center_candidate');

        // Apply filters dynamically
        foreach (['financial_year' => 'year', 'level', 'session', 'center_no' => 'center'] as $col => $param) {
            if ($request->filled($param)) {
                $query->where("center_candidate.$col", $request->$param);
            }
        }

        // Levels
        $levels = (clone $query)->distinct()->pluck('level')->sort()->values();

        // Sessions
        $sessions = (clone $query)->distinct()->pluck('session')->sort()->values();

        // Centers
        $centers = (clone $query)
            ->join('centers', 'center_candidate.center_no', '=', 'centers.center_no')
            ->distinct()
            ->pluck('centers.center_name', 'centers.center_no');

        // Sponsors
        $sponsors = (clone $query)->distinct()->pluck('sponser')->sort()->values();

        return response()->json([
            'levels'   => $levels,
            'sessions' => $sessions,
            'centers'  => $centers,
            'sponsors' => $sponsors,
        ]);
    }

    protected function getPaymentHistory(Request $request)
    {
        $payment_history = FeeCandidateHistory::with('feegroup')
            ->where('candidate_id', $request->candidate_id);

        return DataTables::eloquent($payment_history)
            ->addColumn('amount', fn($row) => "LSL " . number_format($row->amount, 2))
            ->addColumn('fine', fn($row) => "LSL " . number_format($row->fine, 2))
            ->editColumn('created_at', fn($row) => $row->created_at->format('Y M d H:i:s'))
            ->toJson();
    }

    protected function getCandidates(Request $request)
    {


        // Total paid per candidate
        $amountPaidSub = DB::table('fee_candidate_histories')
            ->select('candidate_id', DB::raw('SUM(amount) as total_paid'))
            ->where('status', 1)
            ->groupBy('candidate_id');

        // Total price per fee group
        $priceSub = DB::table('fee_group_details as fgd')
            ->select('fg.id as fee_group_id', DB::raw('SUM(fgd.amount) as total_price'))
            ->join('fee_groups as fg', 'fgd.fee_group_id', '=', 'fg.id')
            ->groupBy('fg.id');

        // Main query
        $candidates = DB::table('centers')
            ->select([
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
                DB::raw('COALESCE(ap.total_paid, 0) AS amount_paid'),
                DB::raw('COALESCE(p.total_price, 0) AS price')
            ])
            ->join('center_candidate', 'center_candidate.center_no', '=', 'centers.center_no')
            ->join('candidates', 'candidates.candidate_no', '=', 'center_candidate.candidate_no')
            ->leftJoinSub($amountPaidSub, 'ap', function ($join) {
                $join->on('ap.candidate_id', '=', 'center_candidate.id');
            })
            ->leftJoin('sessions as s', function ($join) {
                $join->on('s.session', '=', 'center_candidate.session')
                    ->on('s.financial_year', '=', 'center_candidate.financial_year');
            })
            ->leftJoin('levels as l', 'l.level', '=', 'center_candidate.level')
            ->leftJoin('fee_groups as fg', function ($join) {
                $join->on('fg.session_id', '=', 's.id')
                    ->on('fg.level_id', '=', 'l.id')
                    ->whereRaw('fg.candidate_type = CASE center_candidate.type WHEN 1 THEN 1 WHEN 2 THEN 3 ELSE 3 END');
            })
            ->leftJoinSub($priceSub, 'p', function ($join) {
                $join->on('p.fee_group_id', '=', 'fg.id');
            })
            ->leftJoin('candidate_subject as cs', function ($join) {
                $join->on('cs.candidate_no', '=', 'center_candidate.candidate_no')
                    ->on('cs.level', '=', 'center_candidate.level')
                    ->on('cs.session', '=', 'center_candidate.session')
                    ->on('cs.financial_year', '=', 'center_candidate.financial_year');
            })
            ->leftJoin('fee_group_details as fgd', function ($join) {
                $join->on('fgd.fee_group_id', '=', 'fg.id')
                    ->where(function ($q) {
                        $q->whereColumn('fgd.subject_code', 'cs.subject_code')
                            ->orWhere('fgd.subject_code', '-');
                    });
            })
            ->groupBy([
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
            ]);

        // ---- Apply filters

        // Track how many filters applied
        $filterCount = 0;

        foreach (
            [
                'center'  => 'centers.center_no',
                'year'    => 'center_candidate.financial_year',
                'level'   => 'center_candidate.level',
                'sponsor' => 'center_candidate.sponser',
                'session' => 'center_candidate.session'
            ] as $param => $col
        ) {
            if ($request->filled($param)) {
                $candidates->where($col, $request->$param);
                $filterCount++;
            }
        }



        // ---- If less than 3 filters, apply default limit
        if ($filterCount < 2) {
            $candidates->limit(10); // 👈 adjustable
        }

        $candidates->groupBy(
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
            'center_candidate.session'
        )->orderBy('center_candidate.candidate_no', 'asc');

        return DataTables::of($candidates)
            ->setRowId('candidate_no')
            ->editColumn('candidate_no', fn($row) => str_pad($row->candidate_no, 9, '0', STR_PAD_LEFT))
            ->editColumn('national_id', fn($row) => str_pad($row->national_id, 12, '0', STR_PAD_LEFT))
            ->editColumn('amount_paid', fn($row) => "LSL " . number_format($row->amount_paid, 2))
            ->editColumn('actions', fn($row) => "<a class='btn bg-gradient-primary btn-sm approval_btn' data-action='" . route('sponsor.candidate.edit', $row->id) . "' href='javascript:void(0)'>Action</a>")
            ->addColumn('action', fn($row) => '<a href="javascript:void(0)" data-toggle="tooltip" data-url="' . route('admin.fees-stracture.fee-histories.show', $row->id) . '" class="view-history btn btn-primary"><i class="fa fa-arrow-right"></i> Collect</a>')
            ->rawColumns(['action', 'actions'])
            ->toJson();   // 👈 replaces ->make()
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
