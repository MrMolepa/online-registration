<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libraries\Payment\Payment;
use App\Models\CenterCandidate;
use App\Models\FeeCandidateHistory;
use App\Models\FeeGroup;
use App\Models\FeePaymentMethod;
use App\Models\Funder;
use App\Models\FunderPaymentHistory;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SponsorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $years = CenterCandidate::select(DB::raw('financial_year as year'))
            ->orderBy('year', 'DESC')
            ->distinct()
            ->get()->pluck('year');

        if ($request->ajax()) {
            $sponsors = Funder::query();
            return DataTables::of($sponsors)
                ->setRowId('id')
                ->editColumn('sponsor', function ($row) {
                    return  $row->sponsor;
                })
                ->editColumn('name', function ($row) {
                    return  $row->name;
                })
                ->editColumn('description', function ($row) {
                    return $row->description;
                })
                ->editColumn('status', function ($row) {
                    return $row->status ?'Active':'Inactive';
                })
                ->editColumn('actions', function ($row) {
                    $btn = "<button  data-toggle='tooltip'  data-url='" . route('admin.sponsors.edit', $row->id) . "' data-original-title='Edit' class='edit-sponsor  btn btn-primary btn-sm'> <i class='fa fa-edit'></i> Edit </button>
                             <button  data-toggle='tooltip'  data-url='" . route('admin.sponsors.destroy', $row->id) . "' data-original-title='Delete' class='delete-sponsor m-2 btn btn-danger btn-sm'> <i class='fa fa-trash'></i> Delete </button>";
                    return $btn;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        $feegroups = FeeGroup::get();
        $feepaymentmethods = FeePaymentMethod::get();
        return view('admin.finance.sponsor.index', compact('years', 'feegroups', 'feepaymentmethods'));
    }



    public function editSponsorCollection($id)
    {
        $funderPaymentHistory =  FunderPaymentHistory::find($id);
        $url = route('admin.sponsors.updateSponsorCollection', $id);
        return response()->json(['funderPaymentHistory' =>  $funderPaymentHistory, 'url' => $url]);
    }

    public function updateSponsorCollection(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'amount'  => 'required|string',
            'remarks'  => 'required|string',
            'status'  => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $funderPaymentHistory =  FunderPaymentHistory::find($id);

        $feeCandidate =  FeeCandidateHistory::where('reference_no', '=', $funderPaymentHistory->reference_no)->first();
        if (is_null($feeCandidate)) {
            $paymentMethod = FeePaymentMethod::find($request->pay_via);
            $funderPaymentHistory->amount = $request->amount;
            $funderPaymentHistory->status = $request->status;
            $funderPaymentHistory->remarks = $request->remarks;
            $funderPaymentHistory->save();
            DB::table('center_candidate')
                ->select(
                    'center_candidate.id',
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
                 )),0) as exam_fee")
                )
                ->join('candidates', 'candidates.candidate_no', '=', 'center_candidate.candidate_no')
                ->where('center_candidate.level', "=", $funderPaymentHistory->level)
                ->where('center_candidate.sponser', "=", $funderPaymentHistory->sponsor)
                ->where('center_candidate.session', "=", $funderPaymentHistory->session)
                ->where('center_candidate.financial_year', "=", $funderPaymentHistory->financial_year)
                ->orderBy('center_candidate.id', "ASC")
                ->each(function (object $candidate) use (
                    $funderPaymentHistory,
                    $paymentMethod,
                ) {
                    $approval = DB::table('request_action')
                        ->select([DB::raw("count(request_action.id) as status")])
                        ->join('requests', 'requests.id', '=', 'request_action.request_id')
                        ->join('center_candidate', 'center_candidate.id', '=', 'requests.request_data_id')
                        ->join('transitions', 'transitions.id', '=', 'request_action.transition_id')
                        ->join('actions', 'actions.id', '=', 'request_action.action_id')
                        ->join('processes', 'processes.id', '=', 'actions.process')
                        ->join('action_types', 'action_types.id', '=', 'actions.action_type')
                        ->where('requests.request_data', '=', CenterCandidate::class)
                        ->where('center_candidate.id', '=',  $candidate->id)
                        ->where('request_action.is_complete', '=', 1)
                        ->where('action_types.status', '=', 1)
                        ->groupBy('request_action.request_id')
                        ->having(DB::raw("count(request_action.request_id)"), '>', 1)
                        ->first();
                    if ($candidate->amount_paid < $candidate->exam_fee &&  $approval) {
                        FeeCandidateHistory::firstOrCreate([
                            'candidate_id' => $candidate->id,
                            'reference_no' =>  $funderPaymentHistory->reference_no,
                            'amount' =>  $candidate->exam_fee,
                            'fee_group_id' => $candidate->fee_group_id,
                        ], [
                            'candidate_id' => $candidate->id,
                            'reference_no' =>  $funderPaymentHistory->reference_no,
                            'amount' =>  $candidate->exam_fee,
                            'fine' => 0,
                            'fee_group_id' => $candidate->fee_group_id,
                            'attachment' =>  $funderPaymentHistory->attactment,
                            'pay_via' => $funderPaymentHistory->reference_no->id,
                            'collect_by' => Auth::user()->email,
                            'remarks' => "BULK::$paymentMethod->name:$funderPaymentHistory->id",
                            'status' => 1
                        ]);
                    }
                });

            return response()->json(['success' => 'Successfully saved the records']);
        } else {
            return response()->json(['errors' => ['status' => ['reference number already used']]]);
        }
    }
    public function getSponsorAllCollection(Request $request)
    {
        if ($request->ajax()) {

            if ($request->has('payment_history')) {
                $sponsor = $request->sponsor;
                $session = $request->session;
                $level = $request->level;
                $financial_year = $request->financial_year;
                $payment_history = FunderPaymentHistory::where([
                    ['session', '=', $session],
                    ['financial_year', '=', $financial_year],
                    ['level', '=', $level],
                    ['sponsor', '=', $sponsor],
                ]);
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
                    ->editColumn('actions', function ($row) {
                        $url = "";
                        if (!Storage::disk('public')->exists($row->attachment)) {
                            $url = "";
                        }
                        // Get URL
                        $url = asset("$row->attachment");
                        return "<a href='$url'
                                       target='_blank'
                                       class='btn btn-outline-primary btn-sm'
                                       title='View'>
                                        <i class='far fa-eye'></i> View
                                    </a>
                                       <button
                                       data-url='" . route('admin.sponsors.editSponsorCollection', $row->id) . "'
                                       class='btn btn-primary btn-sm edit-collection'
                                      >
                                        <i class='fa fa-edit'></i>approve
                                    </button>
                                    ";
                    })
                    ->rawColumns(['actions'])
                    ->toJson();
            }
            DB::disableQueryLog();
            DB::beginTransaction();
            $sponsors =  DB::table('funders')
                ->select(
                    'center_candidate.level',
                    'center_candidate.session',
                    'center_candidate.financial_year',
                    'funders.sponsor',
                    DB::raw("count(DISTINCT center_candidate.candidate_no ) as candidates"),
                    'name',
                    'description',
                )
                ->join('center_candidate', 'center_candidate.sponser', '=', 'funders.sponsor')
                ->groupBy('center_candidate.sponser', 'center_candidate.level', 'center_candidate.session')
                ->where('center_candidate.financial_year', $request->year)

                ->orderBy('center_candidate.level', "ASC")
                ->orderBy('funders.sponsor', "ASC");
            return DataTables::of($sponsors)
                ->setRowId('id')
                ->editColumn('sponsor', function ($row) {
                    return  $row->sponsor;
                })
                ->editColumn('name', function ($row) {
                    return  $row->name;
                })
                ->editColumn('description', function ($row) {
                    return $row->description;
                })
                ->editColumn('approvals', function ($row) {
                    $approvals = DB::table('request_action')
                        ->select([DB::raw("count(request_action.id) as status")])
                        ->join('requests', 'requests.id', '=', 'request_action.request_id')
                        ->join('center_candidate', 'center_candidate.id', '=', 'requests.request_data_id')
                        ->join('transitions', 'transitions.id', '=', 'request_action.transition_id')
                        ->join('actions', 'actions.id', '=', 'request_action.action_id')
                        ->join('processes', 'processes.id', '=', 'actions.process')
                        ->join('action_types', 'action_types.id', '=', 'actions.action_type')
                        ->where('requests.request_data', '=', CenterCandidate::class)
                        ->where('processes.process_key', '=',  $row->sponsor)
                        ->where('center_candidate.level', '=',  $row->level)
                        ->where('center_candidate.financial_year', '=',  $row->financial_year)
                        ->where('request_action.is_complete', '=', 1)
                        ->where('action_types.status', '=', 1)
                        ->groupBy('request_action.request_id')
                        ->having(DB::raw("count(request_action.request_id)"), '>', 1)
                        ->get()->count();
                    return   $approvals;
                })
                ->editColumn('actions', function ($row) {
                    $approvals = DB::table('request_action')
                        ->select([DB::raw("count(request_action.id) as status")])
                        ->join('requests', 'requests.id', '=', 'request_action.request_id')
                        ->join('center_candidate', 'center_candidate.id', '=', 'requests.request_data_id')
                        ->join('transitions', 'transitions.id', '=', 'request_action.transition_id')
                        ->join('actions', 'actions.id', '=', 'request_action.action_id')
                        ->join('processes', 'processes.id', '=', 'actions.process')
                        ->join('action_types', 'action_types.id', '=', 'actions.action_type')
                        ->where('requests.request_data', '=', CenterCandidate::class)
                        ->where('processes.process_key', '=',  $row->sponsor)
                        ->where('center_candidate.level', '=',  $row->level)
                        ->where('center_candidate.financial_year', '=',  $row->financial_year)
                        ->where('request_action.is_complete', '=', 1)
                        ->where('action_types.status', '=', 1)
                        ->groupBy('request_action.request_id')
                        ->having(DB::raw("count(request_action.request_id)"), '>', 1)
                        ->get()->count();
                    if ($approvals > 0) {
                        $route = route('admin.sponsors.getSponsorCollection', [
                            'sponsor' => $row->sponsor,
                            'session' => $row->session,
                            'level' => $row->level,
                            'financial_year' => $row->financial_year
                        ]);
                        return "<button type='button' class='btn btn-sm btn-primary collected-fee' data-url='$route'>Collect</button>";
                    }
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
    }


    public function getSponsorCollection(Request $request)
    {

        $sponsor = $request->sponsor;
        $session = $request->session;
        $level = $request->level;
        $financial_year = $request->financial_year;
        $grand_total = 0;
        $total_paid = 0;
        $token = md5(rand(1, 5) . microtime());
        DB::table('center_candidate')
            ->select(
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
            ->join('candidates', 'candidates.candidate_no', '=', 'center_candidate.candidate_no')
            ->where('center_candidate.level', "=", $level)
            ->where('center_candidate.sponser', "=", $sponsor)
            ->where('center_candidate.session', "=", $session)
            ->where('center_candidate.financial_year', "=", $financial_year)
            ->orderBy('center_candidate.id', "ASC")
            ->each(function (object $candidate) use (
                &$grand_total,
                &$total_paid,
            ) {
                $grand_total += $candidate->price;
                $total_paid +=  $candidate->amount_paid;
            });



        $sponsor_details = (object)[
            'sponsor' =>  $sponsor,
            'session' =>  $session,
            'level' =>  $level,
            'financial_year' => $financial_year,
            'reference_no' => "$sponsor-$token",
            'grand_total' => $grand_total - $total_paid,
            'total_paid' => $total_paid
        ];

        return response()->json(['sponsor' => $sponsor_details]);
    }


    public function storeSponsorCollection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference_no' => 'required|string',
            'amount'  => 'required|string',
            'fine'  => 'required|string',
            'session'  => 'required|string',
            'level'  => 'required|string',
            'sponsor'  => 'required|string',
            'financial_year'  => 'required|string',
            'attachment'  => 'required',
            'pay_via' => 'required|string',
            'remarks'  => 'required|string',
            'status'  => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        try {
            $financial_year = $request->financial_year;
            $status = $request->status;
            $sponsor = $request->sponsor;


            $reference_no = $request->reference_no;
            $attactment = $request->file('attachment')->storeAs("$financial_year-$sponsor", time() . '.' . $request->file('attachment')->getClientOriginalExtension(),  'public');
            if ($attactment == null ||    $attactment == '') {
                return response()->json(['attachment' => ['Error in storing document in local']]);
            }
            $attactment = "/storage/$attactment";
            DB::beginTransaction();
            $sponsorFee = new FunderPaymentHistory();
            $sponsorFee->sponsor =  $sponsor;
            $sponsorFee->reference_no = $reference_no;
            $sponsorFee->amount = $request->amount;
            $sponsorFee->fine = $request->fine;
            $sponsorFee->financial_year = $financial_year;
            $sponsorFee->session = $request->session;
            $sponsorFee->level = $request->level;
            $sponsorFee->attachment = $attactment;
            $sponsorFee->pay_via = $request->pay_via;
            $sponsorFee->collect_by = Auth::user()->email;
            $sponsorFee->remarks = $request->remarks;
            $sponsorFee->status = $request->status;
            $sponsorFee->save();
            $sponsorFee_id = $sponsorFee->id;
            if ($status == 1) {
                $paymentMethod = FeePaymentMethod::find($request->pay_via);
                DB::table('center_candidate')
                    ->select(
                        'center_candidate.id',
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
                         )),0) as exam_fee")
                    )
                    ->join('candidates', 'candidates.candidate_no', '=', 'center_candidate.candidate_no')
                    ->where('center_candidate.level', "=", $request->level)
                    ->where('center_candidate.sponser', "=", $sponsor)
                    ->where('center_candidate.session', "=", $request->session)
                    ->where('center_candidate.financial_year', "=", $request->financial_year)
                    ->orderBy('center_candidate.id', "ASC")
                    ->each(function (object $candidate) use (
                        $reference_no,
                        $attactment,
                        $paymentMethod,
                        $sponsorFee_id,
                    ) {
                        $approval = DB::table('request_action')
                            ->select([DB::raw("count(request_action.id) as status")])
                            ->join('requests', 'requests.id', '=', 'request_action.request_id')
                            ->join('center_candidate', 'center_candidate.id', '=', 'requests.request_data_id')
                            ->join('transitions', 'transitions.id', '=', 'request_action.transition_id')
                            ->join('actions', 'actions.id', '=', 'request_action.action_id')
                            ->join('processes', 'processes.id', '=', 'actions.process')
                            ->join('action_types', 'action_types.id', '=', 'actions.action_type')
                            ->where('requests.request_data', '=', CenterCandidate::class)
                            ->where('center_candidate.id', '=',  $candidate->id)
                            ->where('request_action.is_complete', '=', 1)
                            ->where('action_types.status', '=', 1)
                            ->groupBy('request_action.request_id')
                            ->having(DB::raw("count(request_action.request_id)"), '>', 1)
                            ->first();
                        if ($candidate->amount_paid < $candidate->exam_fee &&  $approval) {
                            FeeCandidateHistory::firstOrCreate([
                                'candidate_id' => $candidate->id,
                                'reference_no' =>  $reference_no,
                                'amount' =>  $candidate->exam_fee,
                                'fee_group_id' => $candidate->fee_group_id,
                            ], [
                                'candidate_id' => $candidate->id,
                                'reference_no' =>  $reference_no,
                                'amount' =>  $candidate->exam_fee,
                                'fine' => 0,
                                'fee_group_id' => $candidate->fee_group_id,
                                'attachment' =>  $attactment,
                                'pay_via' => $paymentMethod->id,
                                'collect_by' => Auth::user()->email,
                                'remarks' => "BULK::$paymentMethod->name:$sponsorFee_id",
                                'status' => 1
                            ]);
                        }
                    });
            }
            DB::commit();
            return response()->json(['success' => 'Successfully saved the records']);
        } catch (\Exception $e) {
            return response()->json([
                $e->getMessage()
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'sponsor' => "required|unique:funders,sponsor,NULL,id",
            'name' => "required|unique:funders,name,NULL,id",
            'description' => "required|string",
            'status' => "required|string",
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        Funder::create($request->all());
        return response()->json(['success' => "successfully addad the record"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        $sponsor =  Funder::find($id);
        $url = route('admin.sponsors.update', $id);
        return response()->json(['sponsor' =>  $sponsor, 'url' => $url]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {


        $validator = Validator::make($request->all(), [
            'sponsor' => "required|unique:funders,sponsor,$id,id",
            'name' => "required|unique:funders,name,$id,id",
            'status' => "required|string",
            'description' => "required|string",
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $sponsor = Funder::find($id);
        $sponsor->sponsor = $request->sponsor;
        $sponsor->name = $request->name;
        $sponsor->status = $request->status;
        $sponsor->description = $request->description;
        $sponsor->save();
        return response()->json(['success' => "successfully updated the record"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sponsor = Funder::find($id);
        $center_candidate = DB::table('center_candidate')->select(
            [
                'center_candidate.sponser'
            ],
        )->distinct()
            ->where('center_candidate.sponser', '=', $sponsor->sponsor)
            ->orderBy('sponser')
            ->first();
        if (!is_null($center_candidate)) {
            return response()->json(['error' => "Record can not be removed"]);
        } else {
            $sponsor->delete();
            return response()->json(['success' => "successfully updated the record"]);
        }
    }
}
