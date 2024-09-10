<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Process;
use App\Models\State;
use App\Models\StateType;
use App\Models\Transition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TransitionController extends Controller
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
            $transitions =  Transition::whereHas('selectCurrentState')->whereHas('selectNextState')
                ->with('selectCurrentState', 'selectNextState')
                ->where('process', $process_id)->get();
            return DataTables::of($transitions)
                ->setRowId('id')
                ->editColumn('currentState', function ($row) {
                    $states = State::get();

                    $stateHTML = "";
                    $selectedState = "";
                    foreach ($states as   $state) {
                        $stateType = $state->stateType->name;
                        if ($row->selectCurrentState->id == $state->id) {
                            $selectedState .= "$state->name  ($stateType)";
                            $stateHTML .= "<option value='$state->id' selected > $state->name  ($stateType) </option>";
                        } else {
                            $stateHTML .= "<option value='$state->id'>$state->name  ($stateType) </option>";
                        }
                    }


                    $html = "
                    <div class='form-group'>
                        <span class='editSpan period'>   $selectedState  </span>
                        <select class='editInput period form-control' name='current_state' >
                        $stateHTML
                        </select>
                    </div>";
                    return     $html;
                })
                ->editColumn('nextState', function ($row) {
                    $states = State::get();
                    $stateHTML = "";
                    $selectedState = "";
                    foreach ($states as   $state) {
                        $stateType = $state->stateType->name;
                        if ($row->selectNextState->id == $state->id) {
                            $selectedState .= "$state->name ";
                            $stateHTML .= "<option value='$state->id' selected >$state->name ($stateType )</option>";
                        } else {
                            $stateHTML .= "<option value='$state->id'>$state->name ($stateType)</option>";
                        }
                    }

                    $html = "
                    <div class='form-group'>
                        <span class='editSpan period'>   $selectedState  </span>
                        <select class='editInput period form-control' name='next_state' >
                        $stateHTML
                        </select>
                    </div>";
                    return     $html;
                })
                ->editColumn('action', function ($row) {
                    $html = "<button type='button' class='btn btn-sm btn-primary editBtn'><i class='fas fa-edit'></i></button>
                              <button type='button' class='btn btn-sm btn-success saveBtn' data-url='" . route('admin.transitions.update', $row->id) . "'> Save</button>
                              <button type='button' class='btn btn-sm btn-danger deleteBtn' data-url='" . route('admin.transitions.destroy', $row->id) . "'> <i class='fas fa-trash-alt'></i></button>";
                    return     $html;
                })
                ->rawColumns(['currentState', 'nextState', 'action'])
                ->make(true);
        }
        $process = Process::find($request->process_id);
        $states = State::get();
        return view('admin.process.transition', compact('process', 'states'));
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
            'current_state' => 'required',
            'next_state' => 'required',
            'process' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $transition = new Transition();
        $transition->process = $request->process;
        $transition->currentState = $request->current_state;
        $transition->nextState = $request->next_state;
        $transition->save();
        return response()->json(['success' =>  'Successfully  added the records']);
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
            'current_state' => 'required',
            'next_state' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $transition = Transition::find($id);
        $transition->currentState = $request->current_state;
        $transition->nextState = $request->next_state;
        $transition->save();
        return response()->json(['success' =>  'Successfully  updated the records']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $transition = Transition::find($id);
        $transition->delete();
        return response()->json(['success' =>  'Successfully  deleted the records']);
    }
}
