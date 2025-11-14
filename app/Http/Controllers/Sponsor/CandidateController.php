<?php

namespace App\Http\Controllers\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\ActionType;
use App\Models\Candidate;
use App\Models\Center;
use App\Models\CenterCandidate;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CandidateController extends Controller
{

    public function index(Request $request)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(-1);
        $user_type = get_class(Auth::user());
        $user_id = auth()->user()->id;
        $sponsor = auth()->user()->sponsor;

        $user_districts = auth()->user()->districts->pluck('district_code')->toArray();
        $level = auth()->user()->level;
        $approve_order = DB::table('transition_action')
            ->select('order_number')
            ->join('actions', 'actions.id', '=', 'transition_action.action_id')
            ->join('action_types', 'action_types.id', '=', 'actions.action_type')
            ->join('action_user', 'action_user.action_id', '=', 'actions.id')
            ->join('processes', 'processes.id', '=', 'action_user.process')
            ->where('action_types.status', '=', 1)
            ->where('processes.process_key', '=', $sponsor)
            ->where('action_user.user_id', '=', $user_id)
            ->orderBy('actions.order_number', 'ASC')
            ->first();


        $actions = DB::table('actions')
            ->select('action_user.process', 'action_user.user_id', 'action_user.user_type', 'actions.id as action_id', 'action_types.name', 'actions.description')
            ->join('action_user', 'action_user.action_id', '=', 'actions.id')
            ->join('processes', 'processes.id', '=', 'action_user.process')
            ->join('action_types', 'action_types.id', '=', 'actions.action_type')
            ->where('user_type', '=', $user_type)
            ->where('user_id', '=', $user_id)
            ->where('processes.process_key', '=',  $sponsor)
            ->orderBy('action_types.status', 'DESC')
            ->get();


        $process_actions = DB::table('actions')
            ->select('action_user.process', 'action_user.user_id', 'action_user.user_type', 'actions.id as action_id', 'action_types.name', 'actions.description')
            ->join('action_user', 'action_user.action_id', '=', 'actions.id')
            ->join('processes', 'processes.id', '=', 'action_user.process')
            ->join('action_types', 'action_types.id', '=', 'actions.action_type')
            ->where('user_type', '=', $user_type)
            ->where('user_id', '=', $user_id)
            ->where('processes.process_key', '=',  $sponsor)
            ->orderBy('action_types.status', 'DESC')
            ->get();



        $center_candidate =  CenterCandidate::select('session', 'financial_year')
            ->where('level', '=',  $level)
            ->orderBy('financial_year', 'DESC')
            ->distinct()
            ->first();
        $districts = Center::groupBy('district_code')
            ->where('level', '=',  $level)
            ->whereIn('district_code', $user_districts)
            ->get();


        $approved_candidate_id = DB::table('request_action')
            ->select([DB::raw("center_candidate.id")])
            ->join('requests', 'requests.id', '=', 'request_action.request_id')
            ->join('center_candidate', 'center_candidate.id', '=', 'requests.request_data_id')
            ->join('transitions', 'transitions.id', '=', 'request_action.transition_id')
            ->join('actions', 'actions.id', '=', 'request_action.action_id')
            ->join('processes', 'processes.id', '=', 'actions.process')
            ->join('action_types', 'action_types.id', '=', 'actions.action_type')
            ->where('requests.request_data', '=', CenterCandidate::class)
            ->where('processes.process_key', '=',  $sponsor)
            ->where('center_candidate.financial_year', '=',  $center_candidate->financial_year)
            ->where('request_action.is_complete', '=', 1)
            ->where('action_types.name', '=', 'Approve')
            ->groupBy('request_action.request_id')
            ->having(DB::raw("count(request_action.request_id)"), '>', 1)
            ->get()
            ->pluck('id')->toArray();


        $declined_candidate_id = DB::table('request_action')
            ->select([DB::raw("center_candidate.id")])
            ->join('requests', 'requests.id', '=', 'request_action.request_id')
            ->join('center_candidate', 'center_candidate.id', '=', 'requests.request_data_id')
            ->join('transitions', 'transitions.id', '=', 'request_action.transition_id')
            ->join('actions', 'actions.id', '=', 'request_action.action_id')
            ->join('processes', 'processes.id', '=', 'actions.process')
            ->join('action_types', 'action_types.id', '=', 'actions.action_type')
            ->where('requests.request_data', '=', CenterCandidate::class)
            ->where('request_action.is_active', '=', 0)
            ->where('processes.process_key', '=',  $sponsor)
            ->where('center_candidate.financial_year', '=',  $center_candidate->financial_year)
            ->where('request_action.is_complete', '=', 1)
            ->where('action_types.name', '=', 'Decline')
            ->get()
            ->pluck('id')->toArray();





        $all_sponsored_candidates = DB::table('center_candidate')
            ->select('center_candidate.id')
            ->where('center_candidate.sponser', '=',  $sponsor)
            ->where('center_candidate.level', '=',  $level)
            ->where('center_candidate.financial_year', '=', $center_candidate->financial_year)
            ->whereNotIn('center_candidate.id', $approved_candidate_id)
            // ->whereIn('center_candidate.id', $declined_candidate_id)
            ->groupBy('center_candidate.id')
            ->get()->pluck('id')->toArray();







        if ($request->ajax()) {

            $district = $request->district;
            $centre = $request->centre;







            $candidates =  DB::table('centers')
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



            $declined_candidates = DB::table('request_action')
                ->select(
                    'centers.center_no',
                    'centers.center_name',
                    'center_candidate.id',
                    'center_candidate.candidate_no',
                    'center_candidate.national_id',
                    'center_candidate.type',
                    'center_candidate.subject_number',
                    'candidates.candidate_surname',
                    'candidates.candidate_other_name',
                    'candidates.date_of_birth',
                    'candidates.gender'
                )
                ->join('requests', 'requests.id', '=', 'request_action.request_id')
                ->join('center_candidate', 'center_candidate.id', '=', 'requests.request_data_id')
                ->join('centers', 'centers.center_no', '=', 'center_candidate.center_no')
                ->join('candidates', 'candidates.candidate_no', '=', 'center_candidate.candidate_no')
                ->join('transitions', 'transitions.id', '=', 'request_action.transition_id')
                ->join('actions', 'actions.id', '=', 'request_action.action_id')
                ->join('processes', 'processes.id', '=', 'actions.process')
                ->join('action_types', 'action_types.id', '=', 'actions.action_type');








            if ($approve_order->order_number > 1) {
                $candidates = $candidates->join('requests', 'requests.request_data_id', '=', 'center_candidate.id')
                    ->join('request_action', 'request_action.request_id', '=', 'requests.id')
                    ->join('transitions', 'transitions.id', '=', 'request_action.transition_id')
                    ->join('processes', 'processes.id', '=', 'transitions.process')
                    ->join('actions', 'actions.id', '=', 'request_action.action_id')
                    ->join('action_types', 'action_types.id', '=', 'actions.action_type');
            }
            $candidates = $candidates
                ->groupBy('center_candidate.candidate_no')
                ->where('center_candidate.level', '=', $level)
                ->where('center_candidate.sponser', '=', $sponsor);
            if (!empty($district)) {
                $candidates =   $candidates->where('centers.district_code', '=',  $district);
            }
            if (!empty($centre)) {
                $candidates =   $candidates->where('centers.center_no', '=',  $centre);
            }

            $candidates =  $candidates->where('center_candidate.financial_year', '=', $center_candidate->financial_year)
                ->orderBy('centers.center_no', 'ASC')
                ->orderBy('candidates.candidate_surname', "ASC");


            if ($request->declined_candidates) {
                $declined_candidates = $declined_candidates->where('requests.request_data', '=', CenterCandidate::class)
                    ->where('request_action.is_active', '=', 0)
                    ->where('processes.process_key', '=',  $sponsor)
                    ->where('request_action.is_complete', '=', 1)
                    ->where('action_types.status', '=', 0)
                    ->where('center_candidate.financial_year', '=', $center_candidate->financial_year)
                    ->orderBy('centers.center_no', 'ASC')
                    ->orderBy('candidates.candidate_surname', "ASC");
                return DataTables::of($declined_candidates)
                    ->setRowId('candidate_no')
                    ->editColumn('candidate_no', function ($row) {
                        return   str_pad($row->candidate_no, 9, '0', STR_PAD_LEFT);
                    })
                    ->editColumn('national_id', function ($row) {
                        return   str_pad($row->national_id, 12, '0', STR_PAD_LEFT);
                    })
                    ->editColumn('date_of_birth', function ($row) {
                        return $row->date_of_birth;
                    })
                    ->rawColumns(['candidate_no', 'national_id', 'status'])
                    ->make(true);
            }

            if ($request->pending_candidates) {
                $pending_candidates = DB::table('center_candidate')
                    ->select(
                        'centers.center_no',
                        'centers.center_name',
                        'center_candidate.id',
                        'center_candidate.candidate_no',
                        'center_candidate.national_id',
                        'center_candidate.type',
                        'center_candidate.subject_number',
                        'candidates.candidate_surname',
                        'candidates.candidate_other_name',
                        'candidates.date_of_birth',
                        'candidates.gender'
                    )
                    ->join('centers', 'centers.center_no', '=', 'center_candidate.center_no')
                    ->join('candidates', 'candidates.candidate_no', '=', 'center_candidate.candidate_no')
                    ->whereIn('center_candidate.id', $all_sponsored_candidates)
                    ->orderBy('centers.center_no', 'ASC')
                    ->orderBy('candidates.candidate_surname', "ASC");
                return DataTables::of($pending_candidates)
                    ->setRowId('candidate_no')
                    ->editColumn('candidate_no', function ($row) {
                        return   str_pad($row->candidate_no, 9, '0', STR_PAD_LEFT);
                    })
                    ->editColumn('national_id', function ($row) {
                        return   str_pad($row->national_id, 12, '0', STR_PAD_LEFT);
                    })
                    ->editColumn('date_of_birth', function ($row) {
                        return $row->date_of_birth;
                    })
                    ->editColumn('status', function ($row) use ($sponsor) {
                        $request_action = DB::table('request_action')
                            ->select('actions.action_type', 'actions.order_number', 'action_types.status', 'action_types.name', 'action_types.label_color')
                            ->join('requests', 'requests.id', '=', 'request_action.request_id')
                            ->join('transitions', 'transitions.id', '=', 'request_action.transition_id')
                            ->join('processes', 'processes.id', '=', 'transitions.process')
                            ->join('actions', 'actions.id', '=', 'request_action.action_id')
                            ->join('action_types', 'action_types.id', '=', 'actions.action_type')
                            ->where('requests.request_data_id', '=', $row->id)
                            ->where('requests.request_data', '=', CenterCandidate::class)
                            ->where('processes.process_key', '=',  $sponsor)
                            ->where('request_action.is_active', '=', 0)
                            ->where('request_action.is_complete', '=', 1);
                        $all_appovals = $this->getTotalApprovals($sponsor)->count();
                        $candidate_appovals = $request_action->get()->count();
                        $percentage =  ($candidate_appovals / $all_appovals) * 100;
                        if ($percentage == 0) {
                            return "<span class='right badge badge-warning'>$percentage%</span>";
                        } elseif ($percentage > 0 & $percentage < 100) {
                            if ($request_action->first()->status == 0) {
                                $candidate_appovals = $request_action->get()->count();
                                $percentage =  ($candidate_appovals / $all_appovals) * 100;
                                $label = $request_action->first()->label_color;
                                return "<span class='right badge $label'>0%</span>";
                            } else {
                                $label = $request_action->first()->label_color;
                                return "<span class='right badge $label'>$percentage%</span>";
                            }
                        } else {
                            $label = $request_action->first()->label_color;
                            return "<span class='right badge $label'>$percentage%</span>";
                        }
                    })
                    ->rawColumns(['candidate_no', 'national_id', 'status'])
                    ->make(true);
            }

            return DataTables::of($candidates)
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
                ->editColumn('price', function ($row) {
                    return   "LSL " . number_format($row->price, 2, '.', '');
                })
                ->editColumn('date_of_birth', function ($row) {
                    return $row->date_of_birth;
                })
                ->editColumn('status', function ($row) use ($sponsor) {
                    $request_action = DB::table('request_action')
                        ->select('actions.action_type', 'actions.order_number', 'action_types.status', 'action_types.name', 'action_types.label_color')
                        ->join('requests', 'requests.id', '=', 'request_action.request_id')
                        ->join('transitions', 'transitions.id', '=', 'request_action.transition_id')
                        ->join('processes', 'processes.id', '=', 'transitions.process')
                        ->join('actions', 'actions.id', '=', 'request_action.action_id')
                        ->join('action_types', 'action_types.id', '=', 'actions.action_type')
                        ->where('requests.request_data_id', '=', $row->id)
                        ->where('requests.request_data', '=', CenterCandidate::class)
                        ->where('processes.process_key', '=',  $sponsor)
                        ->where('request_action.is_active', '=', 0)
                        ->where('request_action.is_complete', '=', 1);
                    $all_appovals = $this->getTotalApprovals($sponsor)->count();
                    $candidate_appovals = $request_action->get()->count();
                    $percentage =  ($candidate_appovals / $all_appovals) * 100;
                    if ($percentage == 0) {
                        return "<span class='right badge badge-warning'>$percentage%</span>";
                    } elseif ($percentage > 0 & $percentage < 100) {
                        if ($request_action->first()->status == 0) {
                            $candidate_appovals = $request_action->get()->count();
                            $percentage =  ($candidate_appovals / $all_appovals) * 100;
                            $label = $request_action->first()->label_color;
                            return "<span class='right badge $label'>0%</span>";
                        } else {
                            $label = $request_action->first()->label_color;
                            return "<span class='right badge $label'>$percentage%</span>";
                        }
                    } else {
                        $label = $request_action->first()->label_color;
                        return "<span class='right badge $label'>$percentage%</span>";
                    }
                })
                ->rawColumns(['actions', 'candidate_no', 'national_id', 'status'])

                ->make(true);
        }
        return view('sponsor.candidate', compact('districts', 'actions'));
    }

    public function centers(Request $request)
    {

        ini_set('memory_limit', '-1');
        set_time_limit(-1);
        $sponsor = auth()->user()->sponsor;
        $level = auth()->user()->level;
        $center_candidate =  CenterCandidate::select('session', 'financial_year')
            ->where('sponser', '=', $sponsor)
            ->where('level', '=',  $level)
            ->orderBy('financial_year', 'DESC')
            ->distinct()
            ->first();
        $district = $request->district;
        $centre = $request->centre;
        $centers = DB::table('center_candidate')
            ->select(
                [
                    'center_candidate.center_no',
                    'centers.center_name',
                    'centers.district'
                ],
            )->join('centers', 'center_candidate.center_no', '=', 'centers.center_no')
            ->groupBy('center_candidate.center_no')
            ->where('center_candidate.sponser', '=', $sponsor)
            ->where('center_candidate.level', '=', $level)
            ->where('center_candidate.financial_year', '=',   $center_candidate->financial_year)
            ->where('centers.district_code', '=',  $district);
        if ($centre !== null) {
            $centers = $centers->where('centers.center_no', '=',  $centre);
        }
        $centers =  $centers->orderBy('centers.center_no', 'ASC')
            ->get();
        return response()->json(['centers' => $centers]);
    }


    public function edit($id)
    {
        $url = route('sponsor.candidate.update', $id);
        return response()->json(['action' => $url]);
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'action' => 'required',
            'comments' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $sponsor = auth()->user()->sponsor;
        $user_type = get_class(Auth::user());
        $user_id = auth()->user()->id;
        $request_id = $id;
        // Action
        $action = DB::table('actions')
            ->select('action_user.process', 'action_types.status', 'action_user.user_id', 'action_user.user_type', 'actions.id as action_id', 'action_types.name')
            ->join('action_user', 'action_user.action_id', '=', 'actions.id')
            ->join('processes', 'processes.id', '=', 'action_user.process')
            ->join('action_types', 'action_types.id', '=', 'actions.action_type')
            ->where('user_type', '=', $user_type)
            ->where('user_id', '=', $user_id)
            ->where('processes.process_key', '=', $sponsor)
            ->where('actions.id', '=', $request->action)
            ->first();
        // Request Data load data
        $requests = DB::table('requests')
            ->where('request_data_id', '=', $request_id)
            ->where('process', '=', $action->process)
            ->where('request_data', '=', CenterCandidate::class)
            ->first();
        if ($requests == null) {
            DB::table('requests')->insert([
                'process' => $action->process,
                'user_id' => $user_id,
                'current_state' => 1,
                'request_data_id' => $request_id,
                'request_data' => CenterCandidate::class,
            ]);
            $requests = DB::table('requests')
                ->where('request_data_id', '=', $request_id)
                ->where('request_data', '=',  CenterCandidate::class)
                ->where('process', '=', $action->process)
                ->first();
        }


        // Current State
        $current_requests = DB::table('transition_action')
            ->select('transitions.currentState', 'transitions.nextState')
            ->join('transitions', 'transitions.id', '=', 'transition_action.transition_id')
            ->join('actions', 'actions.id', '=', 'transition_action.action_id')
            ->join('action_user', 'action_user.action_id', '=', 'actions.id')
            ->join('processes', 'processes.id', '=', 'action_user.process')
            ->where('processes.process_key', '=', $sponsor)
            ->where('action_user.user_id', '=', $user_id)
            ->where('actions.id', '=', $request->action)
            ->first();




        // DB::table('requests')
        //     ->where('request_data_id', '=', $request_id)
        //     ->where('request_data', '=',  CenterCandidate::class)
        //     ->where('process', '=', $action->process)
        //     ->first();


        $current_state = $current_requests->currentState;
        // At this point, the system looks for all outgoing transitions from State A and finds two of them,
        // Transitions.It then loads the following data into RequestActions
        $transitions = DB::table('transition_action')
            ->select('action_user.user_id', 'actions.name', 'transition_action.id as transition_action_id', 'transition_action.transition_id', 'transition_action.action_id', 'transitions.currentState', 'transitions.nextState')
            ->join('transitions', 'transitions.id', '=', 'transition_action.transition_id')
            ->join('actions', 'actions.id', '=', 'transition_action.action_id')
            ->join('action_user', 'action_user.action_id', '=', 'actions.id')
            ->join('processes', 'processes.id', '=', 'action_user.process')
            ->where('transitions.currentState', '=', $current_state)
            ->where('processes.process_key', '=', $sponsor)
            ->where('action_user.user_id', '=', $user_id)
            ->get();




        $check_user = DB::table('request_action')
            ->join('actions', 'actions.id', '=', 'request_action.action_id')
            ->join('action_user', 'action_user.action_id', '=', 'actions.id')
            ->join('processes', 'processes.id', '=', 'action_user.process')
            ->where('request_action.request_id', '=', $requests->id)
            ->where('processes.process_key', '=', $sponsor)
            ->where('action_user.user_id', '=', $user_id)
            ->where('is_complete', '=', 1)
            ->first();


        $authorizetion = DB::table('request_action')
            ->where('action_id', '=', $request->action)
            ->where('request_id', '=', $requests->id)
            ->where('is_complete', '=', 1)
            ->first();


        if ( $check_user == null && $authorizetion==null ) {
            foreach ($transitions as $transition) {
                $request_action = DB::table('request_action')
                    ->where('action_id', '=', $transition->action_id)
                    ->where('transition_id', '=', $transition->transition_id)
                    ->where('request_id', '=', $requests->id)
                    ->first();
                if ($request_action == null) {
                    DB::table('request_action')->insert([
                        'action_id' => $transition->action_id,
                        'request_id' => $requests->id,
                        'transition_id' => $transition->transition_id,
                        'is_active' => 1,
                        'is_complete' => 0,
                    ]);
                }
            }
            // submits this action:
            // ACTION:
            // User ID: 1
            // ActionType: Approve
            // Request ID: 1
            // (We read that as "User 1 approves Request 1")
            DB::table('request_action')
                ->where('request_id', '=', $requests->id)
                ->where('is_active', '=', 1)
                ->where('is_complete', '=', 0)
                ->where('action_id', '=', $request->action)
                ->update([
                    'is_active' => 0,
                    'is_complete' => 1,
                ]);
            $request_action = DB::table('request_action')
                ->where('request_id', '=', $requests->id)
                ->where('is_active', '=', 0)
                ->where('is_complete', '=', 1)
                ->where('action_id', '=', $request->action)
                ->first();
            if ($request_action !== null) {
                $state = DB::table('transition_action')
                    ->select(
                        'request_action.request_id',
                        'action_user.user_id',
                        'actions.name',
                        'transition_action.id as transition_action_id',
                        'transition_action.transition_id',
                        'transition_action.action_id',
                        'transitions.currentState',
                        'transitions.nextState'
                    )
                    ->leftJoin('transitions', 'transitions.id', '=', 'transition_action.transition_id')
                    ->leftJoin('actions', 'actions.id', '=', 'transition_action.action_id')
                    ->leftJoin('request_action', 'request_action.action_id', '=', 'actions.id')
                    ->leftJoin('action_user', 'action_user.action_id', '=', 'actions.id')
                    ->where('actions.id', '=', $request->action)
                    ->where('action_user.user_id', '=', $user_id)
                    ->where('request_action.request_id', '=', $requests->id)
                    ->first();
                // Move to the next state
                if ($state->nextState != $current_state) {
                    DB::table('request_action')
                        ->join('requests', 'requests.id', '=', 'request_action.request_id')
                        ->where('request_action.request_id', '=', $requests->id)
                        ->where('requests.request_data_id', '=', $request_id)
                        ->where('requests.request_data', '=', CenterCandidate::class)
                        ->where('request_action.is_active', '=', 1)
                        ->where('request_action.is_complete', '=', 0)
                        ->update([
                            'requests.current_state' => $state->nextState,
                        ]);
                    //comments
                    DB::table('request_notes')->insert([
                        'user_id' =>  $user_id,
                        'request_id' => $requests->id,
                        'notes' => $request->comments,
                    ]);
                    if ($action->status == 0) {
                        $candidate = CenterCandidate::find($request_id);
                        $candidate->sponser = "O";
                        $candidate->save();
                    }
                    //send email
                }
            }
            return response()->json(['success' => 'Record has been saved successfully']);
        } else {
            return response()->json(['error' => 'Action already performed waiting for next approver']);
        }
    }




    private function getTotalApprovals($process_key)
    {
        $approvals = DB::table('actions')
            ->select('order_number')
            ->join('action_types', 'action_types.id', '=', 'actions.action_type')
            ->join('processes', 'processes.id', '=', 'actions.process')
            ->where('action_types.status', '=', 1)
            ->where('processes.process_key', '=', $process_key)
            ->orderBy('actions.order_number', 'ASC')
            ->get();
        return  $approvals;
    }

    private function array_equal($a, $b)
    {
        return (
            is_array($a)
            && is_array($b)
            && count($a) == count($b)
            && array_diff($a, $b) === array_diff($b, $a)
        );
    }
}
