<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\ActionType;
use App\Models\AdminUser;
use App\Models\Process;
use App\Models\Role;
use App\Models\SponsorUser;
use App\Models\Transition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ActionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


        //
        $adminUsers = AdminUser::get();
        $adminUsers =   $adminUsers->map(function ($user) {
            $user->type = AdminUser::class;
            return $user;
        });
        $sponporUsers = SponsorUser::get();
        $sponporUsers =   $sponporUsers->map(function ($user) {
            $user->type = SponsorUser::class;
            return $user;
        });

        $roles = Role::get();
        $roles =   $roles->map(function ($role) {
            $role->type = Role::class;
            return $role;
        });
        if ($request->ajax()) {
            ini_set('memory_limit', '-1');
            set_time_limit(-1);
            $process_id = $request->process_id;
            $actions =  Action::with('actionType', 'transitions.selectCurrentState', 'transitions.selectNextState')->whereHas('actionType')
                ->whereHas('transitions')->where('process', $process_id)->get();
            return DataTables::of($actions)
                ->setRowId('id')
                ->editColumn('name', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'>$row->name</span>
                    <input class='editInput period form-control' type='text' name='name' value='$row->name'>
                    </div>
                    ";
                    return     $html;
                })
                ->editColumn('actionType', function ($row) {
                    $actionType = $row->actionType->name;
                    $actiontypes = ActionType::get();
                    $actionTypeHTML = "";
                    $selectedActionType = "";
                    foreach ($actiontypes as  $actionType) {
                        if ($actionType->id == $row->actionType->id) {
                            $selectedActionType .= "$actionType->name ";
                            $actionTypeHTML .= "<option value='$actionType->id' selected >$actionType->name </option>";
                        } else {
                            $actionTypeHTML .= "<option value='$actionType->id'>$actionType->name </option>";
                        }
                    }
                    $html = "
                    <div class='form-group'>
                        <span class='editSpan period'>    $selectedActionType </span>
                        <select class='options-multiple editInput period form-control' name='action_type'>
                        $actionTypeHTML
                        </select>
                    </div>";
                    return     $html;
                })
                ->editColumn('transition', function ($row) {
                    $transitions = Transition::get();
                    $selectedTransitionids = $row->transitions->pluck('id')->toArray();
                    $transitionHTML = "";
                    $selectedTransition = "";
                    foreach ($transitions as  $transition) {
                        $transitionOption = $transition->selectCurrentState->stateType->name . "-" . $transition->selectNextState->stateType->name;
                        if (in_array($transition->id, $selectedTransitionids)) {
                            $selectedTransition = $transition->selectCurrentState->stateType->name . "-" . $transition->selectNextState->stateType->name;
                            $transitionHTML .= "<option value='$transition->id' selected >$transitionOption </option>";
                        } else {
                            $transitionHTML .= "<option value='$transition->id'>$transitionOption </option>";
                        }
                    }
                    $html = "
                    <div class='form-group'>
                        <span class='editSpan period'>  $selectedTransition</span>
                        <select class='transition-multiple editInput period form-control' name='transition_actions[]' multiple='multiple'>
                        $transitionHTML
                        </select>
                    </div>";
                    return     $html;
                })
                ->editColumn('users', function ($row) {
                    $users = DB::table('actions')
                        ->select('action_user.user_id', 'action_user.user_type', 'actions.id', 'actions.name')
                        ->join('action_user', 'action_user.action_id', '=', 'actions.id')
                        ->where('actions.id', '=', $row->id)
                        ->get()->pluck('user_type', 'user_id')->toArray();
                    $adminUsers = AdminUser::get();
                    $adminUsers =   $adminUsers->map(function ($user) {
                        $user->type = AdminUser::class;
                        return $user;
                    });
                    $sponporUsers = SponsorUser::get();
                    $sponporUsers =   $sponporUsers->map(function ($user) {
                        $user->type = SponsorUser::class;
                        return $user;
                    });
                    $userHTML = "";
                    $selectedUsers = "";
                    $userHTML .= "<optgroup label='Sponsor Users'>";
                    foreach ($sponporUsers as $sponsorUser) {
                        if (isset($users[$sponsorUser->id])) {
                            $selectedUsers .= "$sponsorUser->email,";
                            $userHTML .= " <option value='$sponsorUser->id;$sponsorUser->type' selected>$sponsorUser->email </option>";
                        } else {
                            $userHTML .= " <option value='$sponsorUser->id;$sponsorUser->type'>$sponsorUser->email </option>";
                        }
                    }
                    $userHTML .= "</optgroup>
                                <optgroup label='Admin Users'>";
                    foreach ($adminUsers as $adminUser) {
                        if (isset($users[$adminUser->id])) {
                            $selectedUsers .= "$sponsorUser->email,";
                            $userHTML .= " <option value='$adminUser->id;$adminUser->type' selected>$adminUser->email </option>";
                        } else {
                            $userHTML .= " <option value='$adminUser->id;$adminUser->type'>$adminUser->email </option>";
                        }
                    }
                    $userHTML .= " </optgroup>";
                    $html = "
                    <div class='form-group'>
                        <span class='editSpan period'> $selectedUsers </span>
                        <select class='users-multiple editInput period form-control' name='users[]' multiple='multiple'>
                        $userHTML
                        </select>
                    </div>";
                    return     $html;
                })
                ->editColumn('description', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'>$row->description</span>
                    <input class='editInput period form-control' type='text' name='description' value='$row->description'>
                    </div>
                    ";
                    return     $html;
                })
                ->editColumn('action', function ($row) {
                    $html = "<button type='button' class='btn btn-sm btn-primary editBtn'><i class='fas fa-edit'></i></button>
                              <button type='button' class='btn btn-sm btn-success saveBtn' data-url='" . route('admin.actions.update', $row->id) . "'> Save</button>
                              <button type='button' class='btn btn-sm btn-danger deleteBtn' data-url='" . route('admin.actions.destroy', $row->id) . "'> <i class='fas fa-trash-alt'></i></button>";
                    return     $html;
                })
                ->rawColumns(['name', 'description', 'transition', 'users', 'actionType', 'action'])
                ->make(true);
        }
        $process = Process::find($request->process_id);
        $action_types = ActionType::get();
        $transitions =  Transition::whereHas('selectCurrentState')->whereHas('selectNextState')
            ->with('selectCurrentState', 'selectNextState')
            ->where('process', $request->process_id)->get();
        return view('admin.process.action', compact('process', 'action_types', 'transitions', 'adminUsers', 'sponporUsers', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'process' => 'required',
            'description' => 'required',
            'transition_actions' => 'required',
            'action_type' => 'required',
            'users' => 'required|array',

        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $actions_order =  Action::with('actionType', 'transitions.selectCurrentState', 'transitions.selectNextState')
        ->orderBy('order_number', 'DESC')
        ->whereHas('actionType', function ($q) {
            $q->where('status', '=', 1);
        })->whereHas('transitions')
        ->where('process', $request->process)->first();
        $order_number =1;
        if ($actions_order !==null) {
            $order_number =($actions_order->order_number)+1;
        }


        $action = new Action();
        $action->process = $request->process;
        $action->name = $request->name;
        $action->action_type = $request->action_type;
        $action->description = $request->description;
        $action->order_number = $order_number;
        $action->save();
        $action->transitions()->sync($request->transition_actions);
        foreach ($request->users as  $user) {
            $related = explode(';', $user)[1];
            $userId = explode(';', $user)[0];
            $users = [
                $userId => [
                    'user_type' => $related,
                    'process' =>  $request->process
                ],
            ];
            $action->users($related)->syncWithoutDetaching($users);
        }
        return response()->json(['success' =>  'Successfully added the records']);
    }




    public function approvalOrder(Request $request)
    {
        if ($request->ajax()) {
            ini_set('memory_limit', '-1');
            set_time_limit(-1);
            $process_id = $request->process_id;
            if ($request->has('orders')) {
                $actions =  Action::with('actionType', 'transitions.selectCurrentState', 'transitions.selectNextState')
                ->orderBy('order_number', 'ASC')
                ->whereHas('actionType', function ($q) {
                    $q->where('status', '=', 1);
                })->whereHas('transitions')
                ->where('process', $process_id)->get();

                foreach ($actions as  $action) {
                    $action->timestamps = false; // To disable update_at field updation
                    $id = $action->id;
                    foreach ($request->orders as $order) {
                        if ($order['id'] == $id) {
                            $action->update(['order_number' => $order['position']]);
                        }
                    }
                }

                return response()->json(['status' =>  'success']);
            }
            $actions =  Action::with('actionType', 'transitions.selectCurrentState', 'transitions.selectNextState')
                ->orderBy('order_number', 'ASC')
                ->whereHas('actionType', function ($q) {
                    $q->where('status', '=', 1);
                })->whereHas('transitions')->where('process', $process_id)->get();
            return DataTables::of($actions)
                ->setRowAttr([
                    'data-id' => function ($row) {
                        return $row->id;
                    },
                ])
                ->editColumn('users', function ($row) {
                    $users = DB::table('actions')
                        ->select('action_user.user_id', 'action_user.user_type', 'actions.id', 'actions.name')
                        ->join('action_user', 'action_user.action_id', '=', 'actions.id')
                        ->where('actions.id', '=', $row->id)
                        ->get()->pluck('user_type', 'user_id')->toArray();
                    $adminUsers = AdminUser::get();
                    $adminUsers =   $adminUsers->map(function ($user) {
                        $user->type = AdminUser::class;
                        return $user;
                    });
                    $sponporUsers = SponsorUser::get();
                    $sponporUsers =   $sponporUsers->map(function ($user) {
                        $user->type = SponsorUser::class;
                        return $user;
                    });
                    $userHTML = "";
                    $selectedUsers = "";
                    $userHTML .= "<optgroup label='Sponsor Users'>";
                    foreach ($sponporUsers as $sponsorUser) {
                        if (isset($users[$sponsorUser->id])) {
                            $selectedUsers .= "$sponsorUser->email,";
                            $userHTML .= " <option value='$sponsorUser->id;$sponsorUser->type' selected>$sponsorUser->email </option>";
                        } else {
                            $userHTML .= " <option value='$sponsorUser->id;$sponsorUser->type'>$sponsorUser->email </option>";
                        }
                    }
                    $userHTML .= "</optgroup>
                                <optgroup label='Admin Users'>";
                    foreach ($adminUsers as $adminUser) {
                        if (isset($users[$adminUser->id])) {
                            $selectedUsers .= "$sponsorUser->email,";
                            $userHTML .= " <option value='$adminUser->id;$adminUser->type' selected>$adminUser->email </option>";
                        } else {
                            $userHTML .= " <option value='$adminUser->id;$adminUser->type'>$adminUser->email </option>";
                        }
                    }
                    $userHTML .= " </optgroup>";
                    $html = "
                    <div class='form-group'>
                        <span class='editSpan period'> $selectedUsers </span>
                        <select class='users-multiple editInput period form-control' name='users[]' multiple='multiple'>
                        $userHTML
                        </select>
                    </div>";
                    return     $html;
                })
                ->editColumn('description', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'>$row->description</span>
                    <input class='editInput period form-control' type='text' name='description' value='$row->description'>
                    </div>
                    ";
                    return     $html;
                })
                ->editColumn('order', function ($row) {
                    $html = "<div style='color:rgb(124,77,255); padding-left: 10px; float: left; font-size: 20px; cursor: pointer;' title='change display order'>
                                <i class='fa fa-ellipsis-v'></i>
                                <i class='fa fa-ellipsis-v'></i>
                                </div>
                            ";
                    return     $html;
                })
                ->rawColumns(['order', 'description', 'users'])
                ->make(true);
        }
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
        //
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
            'name' => 'required',
            'description' => 'required',
            'transition_actions' => 'required',
            'action_type' => 'required',
            'users' => 'required|array',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $action = Action::find($id);
        $action->name = $request->name;
        $action->action_type = $request->action_type;
        $action->description = $request->description;
        $action->save();
        $action->transitions()->sync($request->transition_actions);
        $users = DB::table('actions')
            ->select('action_user.user_id', 'action_user.user_type', 'actions.id', 'actions.name')
            ->join('action_user', 'action_user.action_id', '=', 'actions.id')
            ->where('actions.id', '=', $id)
            ->get();
        foreach ($users as $user) {
            DB::table('action_user')
                ->where('action_id', '=', $id)
                ->where('user_id', '=', $user->user_id)
                ->where('user_type', '=', $user->user_type)
                ->delete();
        }
        foreach ($request->users as  $user) {
            $related = explode(';', $user)[1];
            $userId = explode(';', $user)[0];
            $users = [
                $userId => [
                    'user_type' => $related,
                    'process' =>  $action->process
                ],
            ];
            $action->users($related)->syncWithoutDetaching($users);
        }
        return response()->json(['success' =>  'Successfully updated the records']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // delete the relationships with Tags (Pivot table) first.
        Action::find($id)->transitions()->detach();
        $users = DB::table('actions')
            ->select('action_user.user_id', 'action_user.user_type', 'actions.id', 'actions.name')
            ->join('action_user', 'action_user.action_id', '=', 'actions.id')
            ->where('actions.id', '=', $id)
            ->get();
        foreach ($users as $user) {
            DB::table('action_user')
                ->where('action_id', '=', $id)
                ->where('user_id', '=', $user->user_id)
                ->where('user_type', '=', $user->user_type)
                ->delete();
        }
        $action = Action::find($id);
        $action->delete();
        return response()->json(['success' =>  'Successfully deleted the records']);
    }
}
