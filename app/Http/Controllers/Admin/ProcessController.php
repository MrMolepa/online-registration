<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\Process;
use App\Models\Role;
use App\Models\SponsorUser;
use App\Models\State;
use App\Models\StateType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ProcessController extends Controller
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


        if ($request->has('orders')) {

            $processes = Process::all();
            foreach ($processes as  $process) {
                $process->timestamps = false; // To disable update_at field updation
                $id = $process->id;
                foreach ($request->orders as $order) {
                    if ($order['id'] == $id) {
                        $process->update(['display_order' => $order['position']]);
                    }
                }
            }
            return response('Update Successfully.', 200);
        }

        if ($request->ajax()) {
            ini_set('memory_limit', '-1');
            set_time_limit(-1);
            $processes =  Process::query();
            return DataTables::eloquent($processes)
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
                ->editColumn('description', function ($row) {
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'> $row->description</span>
                    <input class='editInput period form-control' type='text' name='description' value='$row->description'>
                    </div>";
                    return     $html;
                })
                ->editColumn('process_key', function ($row) {
                    $html = "<div class='form-group'>
                            <span class='editSpan period'> $row->process_key</span>
                            <input class='editInput period form-control' type='text' name='process_key' value='$row->process_key'>
                            </div>";
                    return     $html;
                })

                ->editColumn('transition', function ($row) {
                    $url = route('admin.transitions.index', ['process_id' => $row->id]);
                    $html = "<a href='$url' class='btn btn-xs btn-primary'> <i class='fas fa-arrows-alt'></i></a>";
                    return     $html;
                })
                ->editColumn('states', function ($row) {
                    $url = route('admin.states.index', ['process_id' => $row->id]);
                    $html = "<a href='$url' class='btn btn-xs btn-primary'><i class='fas fa-signal'></i></a>";
                    return     $html;
                })
                ->editColumn('process_actions', function ($row) {
                    $url = route('admin.actions.index', ['process_id' => $row->id]);
                    $html = "<a href='$url' class='btn btn-xs btn-primary'><i class='fas fa-radiation-alt'></i></a>";
                    return     $html;
                })
                ->editColumn('activities', function ($row) {
                    $url = route('admin.activities.index', ['process_id' => $row->id]);
                    $html = "<a href='$url' class='btn btn-xs btn-primary'><i class='fas fa-envelope-square'></i></a>";
                    return     $html;
                })
                ->editColumn('action', function ($row) {
                    $html = "<button type='button' class='btn btn-xs btn-primary editBtn'><i class='fas fa-edit'></i></button>
                              <button type='button' class='btn btn-xs btn-success saveBtn' data-url='" . route('admin.processes.update', $row->id) . "'> Save</button>
                              <button type='button' class='btn btn-xs btn-danger deleteBtn' data-url='" . route('admin.processes.destroy', $row->id) . "'> <i class='fas fa-trash-alt'></i></button>";
                    return     $html;
                })
                ->rawColumns([ 'name','process_key', 'description', 'transition','initial_state', 'states', 'activities', 'action', 'process_actions'])
                ->toJson();
        }

        $states =State::get();

        return view('admin.process.process', compact('states'));
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
            'description' => 'required',
            'process_key' => ['unique:processes,process_key','min:1','max:1'],
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $process = new Process();
        $process->name = $request->name;
        $process->description = $request->description;
        // Always 1
        $process->initial_state = 1;
        $process->process_key = $request->process_key;
        $process->save();
        $related = AdminUser::class;
        $userId = Auth()->user()->id;
        $user = [
            $userId => ['user_type' => $related],
        ];
        $process->users($related)->sync($user);
        return response()->json(['success' => "Successfully added the rocords"]);
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
            'process_key' => 'unique:processes,process_key'.$id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $process =Process::find($id);
        $process->name = $request->name;
        $process->description = $request->description;
        $process->process_key = $request->process_key;
        $process->save();
        $related = AdminUser::class;
        $userId = Auth()->user()->id;
        $user = [
            $userId => ['user_type' => $related],
        ];
        $process->users($related)->sync($user);
        return response()->json(['success' => "Successfully updated the rocords"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $process =Process::find($id);
        $process->delete();
        return response()->json(['success' => "Successfully deleted the rocords"]);
    }
}
