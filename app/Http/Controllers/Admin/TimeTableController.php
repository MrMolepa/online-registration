<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimeTableController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $level = $request->level;
            $session = $request->session;
            $value = '';
            $switch_checked = "";
            $checked = "";

            if ($request->has('publisher')) {
                DB::table('publications')
                    ->where(['level' => $level, 'session' => $session])  // find your user by their email
                    ->update([
                        'publish' => $request->publisher,
                    ]);
            }


            if (is_publised($level, $session)) {
                $switch_checked = "switch3-checked";
                $checked = "checked";
            }
            $switcher = "<div class='logs'>
                            <label class='switch3 $switch_checked'>
                                <input class='timetable-publisher' name='publisher' value='1' type='checkbox' $checked>
                                <div></div>
                            </label>
                        </div>";
            $value = "
                 $switcher
                  <table class='table table-striped'>
                    <thead>
                        <tr>
                            <th>Level</th>
                            <th>Session</th>
                            <th>Subject Reference</th>
                            <th>Subject Name</th>
                            <th>Component</th>
                            <th>Date and  start time</th>
                            <th>Ended time</th>
                            <th>Action</th>
                        </tr>
                    </thead>";

            $results =  DB::table('timetable')
                ->where('level', '=', $level)
                ->where('session', '=',  $session)
                ->get();
            foreach ($results as $result) {
                $newDate = date('Y-m-d\TH:i:s', strtotime($result->date_time));
                $value .= '<tr id="' . $result->id . '">
                            <td>' . $result->level . '</td>
                            <td>' . $result->session . '</td>
                            <td>' . $result->subject_code . '</td>
                            <td>' . $result->subject_name . '</td>
                            <td>' . $result->paper_no . "   " . $result->pape_desc . '</td>
                            <td>
                                    <span class="editSpan dateTime"> ' . date('d-m-Y  H:i', strtotime($result->date_time)) . '</span>
                                    <input class="editInput dateTime" type="datetime-local" name="dateTime" value="' . $newDate . '">

                            </td>
                            <td>
                                <span class="editSpan period"> ' . date("H:i", strtotime($result->endTime)) . '</span>
                                <input class="editInput period" type="time" name="period" value="' . date("H:i:s", strtotime($result->endTime)) . '">

                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary editBtn" >Edit</button>
                                <button type="button" class="btn btn-sm btn-success saveBtn" >Save</button>
                            </td>
                    </tr>';
            }
            $value .= '</tbody>
                 </table>';
            return response()->json(['status' => 1, 'table' => $value]);
        }
        $levels = Level::get();
        $sessions = Session::groupBy('session')->get();
        return view('admin.timetable.timetable', compact('levels', 'sessions'));
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $date_time = date("Y-m-d\TH:i:s", strtotime($request->dateTime));
        $endTime = date("H:i:s", strtotime($request->period));
        try {
            DB::table('timetable')
                ->where('id', '=', $id)  // find your user by their email
                ->update(array(
                    'date_time' => $date_time,
                    'endTime' => $endTime
                ));
            return response()->json(['status' => 1]);
        } catch (\Exception $e) {
            return response()->json(['status' => $e]);
        }
    }
}
