<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Models\CenterCandidate;
use App\Models\InvigilationProprietor;
use App\Models\InvigilatorProfile;
use App\Models\Level;
use App\Models\Session;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InviglationProprietorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if ($request->has('center_filter')) {
                $session =   $request->input('session');
                $level = $request->input('level');
                $centers =  DB::table('centers')
                    ->select('centers.center_no', 'centers.center_name', 'center_candidate.level', 'center_candidate.session')
                    ->join('center_candidate', 'center_candidate.center_no', '=', 'centers.center_no');
                if (!empty($session)) {
                    $session = Session::find($session);
                    $centers = $centers->where('center_candidate.session', '=', $session->session)
                        ->where('center_candidate.financial_year', '=', $session->financial_year);
                }
                if (!empty($level)) {
                    $centers =   $centers->where('center_candidate.level', '=', $level);
                }
                $centers =  $centers->groupBy('centers.center_no')
                    ->orderBy('centers.center_no')
                    ->get();
                return response()->json(['centers' =>  $centers]);
            }
            if ($request->has('center_sessions')) {
                $session =   $request->input('session');
                $center_no = $request->input('center_no');
                $session = Session::find($session);
                $center = Center::with('subjects')->where('center_no', '=',    $center_no)->first();
                $subjects = $center->subjects->pluck('subject_code')->toArray();
                $timetables = Timetable::select('id', 'subject_code', 'paper_no', 'subject_name', 'date_time')
                    ->where('session', '=',  $session->session)
                    ->whereIn('subject_code', $subjects)
                    ->orderBy('date_time')
                    ->get();
                    
                $invigilators = InvigilatorProfile::with('invigilation_role.invigilation_type', 'invigilation_status')
                    ->where('invigilator_profile.center_no', '=', $center_no)
                    ->get();


                $output = "<table class='table table-condensed table-striped' id='timesheet-proprietor'>
                    <thead>
                        <tr>
                            <th>Proprietor Source <span class='source-total'></span></th>
                            <th>proprietor Target <span class='target-total'></span></th>
                        </tr>
                    </thead>
                    <tbody>";
                foreach ($timetables as $timetable) {
                    $exams_date = date('Y-M-d H:i', strtotime($timetable->date_time));
                    $output .= "<tr>
                                <td> <label class='btn btn-default'>
                                    <input type='checkbox' class='timesheet-$timetable->id-$timetable->subject_code' name='timesheet_source[]' checked value='$timetable->id' />($timetable->subject_code $timetable->paper_no) $exams_date
                                 </label>
                                </td>
                                <td> <label class='btn btn-default'>
                                      <input type='checkbox' class='timesheet-$timetable->id-$timetable->subject_code'  name='timesheet_target[]' value='$timetable->id' /> ($timetable->subject_code $timetable->paper_no) $exams_date
                                   </label>
                                </td>
                            </tr>";
                }
                $output  .= " </tbody>
                </table>";


                return response()->json(['invigilators' => $invigilators, 'timetables' => $output]);
            }
        }

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
            'center_no' => 'required',
            'session' => 'required',
            'level' => 'required',
            'proprietor_source' => 'required',
            'proprietor_target' => 'required|required|different:proprietor_source',
            'timesheet_source' => 'required|array',
            'timesheet_target' => 'required|array'

        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $proprietor_source = InvigilatorProfile::find($request->proprietor_source);
        $proprietor_source->timesheet()->sync($request->timesheet_source);
        $proprietor_target = InvigilatorProfile::find($request->proprietor_target);
        $proprietor_target->timesheet()->sync($request->timesheet_target);
        InvigilationProprietor::updateOrCreate(
            ['proprietor_target' => $request->proprietor_target],
            ['proprietor_source' => $request->proprietor_source]
        );
        return response()->json(['success' => 'proprietor added successfully!']);
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
