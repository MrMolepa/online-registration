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



        $center_candidate =  CenterCandidate::select('session', 'financial_year')
            ->where('sponser', '=', $sponsor)
            ->where('level', '=',  $level)
            ->orderBy('financial_year', 'DESC')
            ->distinct()
            ->first();


        $districts = Center::groupBy('district_code')
            ->where('level', '=',  $level)
            ->whereIn('district_code', $user_districts)
            ->get();
        if ($request->ajax()) {
            $fee_level = strtolower($level);
            $schoolFees = DB::table('fees_stracture')->select(
                [
                    'fees_stracture.candidate_type',
                    'fees_stracture.subject_fee',
                    'fees_stracture.registration_fee',
                    'fees_stracture.local_fee',
                    'fees_stracture.practical_subject_fee',
                    'fees_stracture.delf_fee',
                    'fees_stracture.bank_charge',
                    'fees_stracture.financial_year',
                ]
            )->join('sessions', function ($join) {
                $join->on('fees_stracture.session', '=', 'sessions.id');
                $join->on('fees_stracture.financial_year', '=', 'sessions.financial_year');
            })
                ->where('fees_stracture.candidate_type', '=', "$fee_level-school")
                ->where('sessions.session', '=', $center_candidate->session)
                ->where('fees_stracture.financial_year', '=', $center_candidate->financial_year)
                ->first();
            $schoolPrivate = DB::table('fees_stracture')->select(
                [
                    'fees_stracture.candidate_type',
                    'fees_stracture.subject_fee',
                    'fees_stracture.registration_fee',
                    'fees_stracture.local_fee',
                    'fees_stracture.practical_subject_fee',
                    'fees_stracture.delf_fee',
                    'fees_stracture.bank_charge',
                    'fees_stracture.financial_year'
                ]
            )->join('sessions', function ($join) {
                $join->on('fees_stracture.session', '=', 'sessions.id');
                $join->on('fees_stracture.financial_year', '=', 'sessions.financial_year');
            })->where('fees_stracture.candidate_type', '=', "$fee_level-private")
                ->where('sessions.session', '=', $center_candidate->session)
                ->where('fees_stracture.financial_year', '=', $center_candidate->financial_year)
                ->first();
            $practicalSubjects = ['0179', '0189', '0191', '0192', '0194', '0417', '0190'];

            $delf = Subject::whereHas('selectedDiscipline', function ($q) {
                $q->where('name', '=', 'LGCSE7');
            })->get()->pluck('subject_code')->toArray();

            $district = $request->district;
            $centre = $request->centre;
            $candidates =  DB::table('candidate_subject')
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
                    'candidates.gender',
                    'center_candidate.sponser',
                    DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code,' ',candidate_subject.subject_option)
                                order by candidate_subject.subject_code separator ',') as subjects")
                )
                ->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
                ->join('center_candidate', function ($join) {
                    $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                    $join->on('candidate_subject.level', '=', 'center_candidate.level');
                    $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
                    $join->on('candidate_subject.session', '=', 'center_candidate.session');
                });


            $declined_candidates = DB::table('request_action')
                ->select('centers.center_no',
                    'centers.center_name',
                    'center_candidate.id',
                    'center_candidate.candidate_no',
                    'center_candidate.national_id',
                    'center_candidate.type',
                    'center_candidate.subject_number',
                    'candidates.candidate_surname',
                    'candidates.candidate_other_name',
                    'candidates.date_of_birth',
                    'candidates.gender')
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
            $candidates = $candidates->join('centers', 'centers.center_no', '=', 'center_candidate.center_no')
                ->groupBy('center_candidate.candidate_no')
                ->where('center_candidate.level', '=', $level)
                ->where('center_candidate.sponser', '=', $sponsor);
            if (!empty($district)) {
                $candidates =   $candidates->where('centers.district_code', '=',  $district);
                // $declined_candidates =    $declined_candidates->where('centers.district_code', '=',  $district);
            }
            if (!empty($centre)) {
                $candidates =   $candidates->where('centers.center_no', '=',  $centre);
                // $declined_candidates =   $declined_candidates->where('centers.center_no', '=',  $centre);
            }

            $candidates =  $candidates->where('center_candidate.financial_year', '=', $center_candidate->financial_year)
                ->orderBy('centers.center_no', 'ASC')
                ->orderBy('candidates.candidate_surname', "ASC");


            if ($request->declined_candidates) {
                $declined_candidates = $declined_candidates->where('requests.request_data', '=', CenterCandidate::class)
                    ->where('request_action.is_active', '=', 0)
                    ->where('processes.process_key', '=',  $sponsor)
                    ->where('request_action.is_complete', '=', 1)
                    ->where('action_types.name', '=', 'Decline')
                    ->orderBy('centers.center_no', 'ASC')
                     ->orderBy('candidates.candidate_surname', "ASC");;

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
                    ->rawColumns(['actions', 'candidate_no', 'national_id', 'status'])
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
                ->editColumn('price', function ($row) use ($schoolFees, $schoolPrivate, $practicalSubjects, $delf) {
                    $total_amount = 0;
                    $subjects = explode(",", $row->subjects);
                    if (in_array($row->type, [2, 3])) {
                        foreach ($subjects as $subject) {
                            $subject_code = explode(" ", $subject);
                            if (in_array($subject_code[0], $practicalSubjects)) {
                                $total_amount += ($schoolPrivate->subject_fee) + $schoolPrivate->practical_subject_fee;
                            } else if (in_array($subject, $delf)) {
                                $total_amount += ($schoolPrivate->delf_fee);
                            } else {
                                $total_amount += ($schoolPrivate->subject_fee);
                            }
                        }
                        $total_amount  += ($schoolPrivate->local_fee + $schoolPrivate->registration_fee);
                    } else {
                        foreach ($subjects as $subject) {
                            $subject_code = explode(" ", $subject);
                            if (in_array($subject_code[0], $practicalSubjects)) {
                                $total_amount += ($schoolFees->subject_fee) + $schoolFees->practical_subject_fee;
                            } else if (in_array($subject_code[0], $delf)) {
                                $total_amount += ($schoolFees->delf_fee);
                            } else {
                                $total_amount += ($schoolFees->subject_fee);
                            }
                        }
                        $total_amount  +=  $schoolFees->local_fee + $schoolFees->registration_fee;
                    }

                    return   "LSL " . number_format($total_amount, 2, '.', '');
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
        $user_type = get_class(Auth::user());
        $user_id = auth()->user()->id;
        $request_id = $id;
        // Action
        $action = DB::table('actions')
            ->select('action_user.process', 'action_types.status', 'action_user.user_id', 'action_user.user_type', 'actions.id as action_id', 'action_types.name')
            ->join('action_user', 'action_user.action_id', '=', 'actions.id')
            ->join('action_types', 'action_types.id', '=', 'actions.action_type')
            ->where('user_type', '=', $user_type)
            ->where('user_id', '=', $user_id)
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
        $current_state = $requests->current_state;
        // At this point, the system looks for all outgoing transitions from State A and finds two of them,
        // Transitions.It then loads the following data into RequestActions

        // return response()->json(['error' => 'Not authorized.'],403);

        $transitions = DB::table('transition_action')
            ->select('action_user.user_id', 'actions.name', 'transition_action.id as transition_action_id', 'transition_action.transition_id', 'transition_action.action_id', 'transitions.currentState', 'transitions.nextState')
            ->join('transitions', 'transitions.id', '=', 'transition_action.transition_id')
            ->join('actions', 'actions.id', '=', 'transition_action.action_id')
            ->join('action_user', 'action_user.action_id', '=', 'actions.id')
            //->where('transitions.currentState', '=', $current_state)
            ->where('action_user.user_id', '=', $user_id)
            ->get();
        $isauthorized = $transitions->first() !== null ? true : false;
        if ($isauthorized) {
            $is_action = DB::table('request_action')
                ->where('action_id', '=', $request->action)
                ->where('transition_id', '=', $transitions->first()->transition_id)
                ->where('request_id', '=', $requests->id)
                ->orWhere(function ($query) {
                    $query->where('is_complete', '=', 1)
                        ->where('is_complete', '=', 0);
                })
                ->first();
            if ($is_action == null) {
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
                                'request_action.is_active' => 0,
                                'request_action.is_complete' => 0,
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
