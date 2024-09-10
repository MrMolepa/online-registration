<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\Process;
use App\Models\State;
use App\Models\StateType;
use App\Models\Transition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            ini_set('memory_limit', '-1');
            set_time_limit(-1);
            $process_id = $request->process_id;


            $activities =  Activity::where('process', $process_id)->get();
            return DataTables::of($activities)
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
                ->editColumn('activityType', function ($row) {
                    $activityType = $row->activityType->name;
                    $html = "
                    <div class='form-group'>
                    <span class='editSpan period'> $activityType</span>
                    <input class='editInput period form-control' type='text' name='name' value='$activityType'>
                    </div>
                    ";
                    return     $html;
                })
                ->editColumn('transition', function ($row) {

                    $url = route('admin.transitions.index', ['process_id' => $row->process]);
                    $html = "<a href='$url' class='btn btn-sm btn-primary'> <i class='fas fa-arrows-alt'></i></a>";
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
                              <button type='button' class='btn btn-sm btn-success saveBtn' data-url='" . route('admin.states.update', $row->id) . "'> Save</button>
                              <button type='button' class='btn btn-sm btn-danger deleteBtn' data-url='" . route('admin.states.destroy', $row->id) . "'> <i class='fas fa-trash-alt'></i></button>";
                    return     $html;
                })
                ->rawColumns(['name', 'description', 'transition', 'activityType', 'action'])
                ->make(true);
        }
        $process = Process::find($request->process_id);
        $activity_types = ActivityType::get();
        $transitions =  Transition::whereHas('selectCurrentState')->whereHas('selectNextState')
            ->with('selectCurrentState', 'selectNextState')
            ->where('process', $request->process_id)->get();
        return view('admin.process.activity', compact('process', 'activity_types', 'transitions'));
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
            'transition_activity' => 'required',
            'activity_type' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $activity = new Activity();
        $activity->process = $request->process;
        $activity->name = $request->name;
        $activity->activity_type = $request->activity_type;
        $activity->description = $request->description;
        $activity->save();
        $activity->transitions()->sync($request->transition_activity);
        return response()->json(['success' =>  'Successfully added the records']);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
