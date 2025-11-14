<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\InvigilatorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvigilatorAttendanceController extends Controller
{
    public function index(Request $request)
    {


        $attendances = Attendance::with('Invigilator')->get();
        // dd( $attendances);

        if ($request->ajax()) {
            $attendances = Attendance::with('Invigilator')->get();
            $output='';
            if (count($attendances) > 0) {
                $output = "<table class='table table-condensed table-striped'>
                <thead>
                    <tr>
                        <th>Center No</th>
                        <th>Surname</th>
                        <th>Other Name(s)</th>
                        <th>Day</th>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time out</th>
                    </tr>
                </thead>
                <tbody>";
                foreach ($attendances as $attendance) {
                    $Invigilator = $attendance->Invigilator;
                    $output .= "<tr>
                            <td>$Invigilator->center_no </td>
                            <td>$Invigilator->surname </td>
                            <td>$Invigilator->other_names </td>
                             <td>$attendance->day </td>
                             <td>$attendance->date </td>
                            <td>$attendance->time_in </td>
                             <td>$attendance->time_out </td>

                        </tr>";

                }


                $output  .= " </tbody>
            </table>";
            }
            return response()->json(['attendances' =>  $output]);

        }
        return view('attendance.invigilator.index');
    }



    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'qr-code' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $qr_code = $request->get('qr-code');
        $id = explode(':', $qr_code[0])[1];
        $invigilator = InvigilatorProfile::find($id);

        $attendance = new Attendance();
        $attendance->profile_id = $invigilator->id;
        $attendance->day=date ('l');
        $attendance->time_in=date("H:i:s");
        $attendance->save();
        return response()->json(['success' =>  $invigilator]);
    }
}
