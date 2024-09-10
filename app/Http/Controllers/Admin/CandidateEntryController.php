<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Models\CenterCandidate;
use App\Models\Level;
use App\Models\OptionHeader;
use App\Models\Session;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CandidateEntryController extends Controller
{
    public function index(Request $request)
    {
        $years =  CenterCandidate::select(DB::raw('financial_year as year'))
            ->orderBy('year', 'DESC')
            ->distinct()
            ->get()->pluck('year');

        $sponsors = DB::table('center_candidate')->select(
            [
                'center_candidate.sponser'
            ],
        )->distinct()
            ->orderBy('sponser')
            ->get();
        $options = OptionHeader::get();

        if ($request->ajax()) {
            $level = $request->level;
            $session = $request->session;
            $center = $request->center;
            $subject = $request->subject;
            $option = $request->option;
            $year = $request->year;
            $sponsor = $request->sponsor;
            $output = "";
            $candidates_entries = DB::table('centers_entries')
                ->select(
                    [
                        'centers_entries.center_no',
                        'centers_entries.center_name',
                        'centers_entries.district',
                        'centers_entries.level',
                        'centers_entries.financial_year',
                        'centers_entries.session',
                        'centers_entries.subject_code',
                        'centers_entries.subject_option',
                        'centers_entries.subject_name',
                        'centers_entries.paper_no',
                        'centers_entries.pape_desc',
                        'centers_entries.subject_number',
                    ],
                );
            if (!is_null($level)) {
                $candidates_entries =  $candidates_entries->where('level', '=', $level);
            }
            if (!is_null($session)) {
                $candidates_entries =  $candidates_entries->where('session', '=', $session);
            }
            if (!is_null($center)) {
                $candidates_entries = $candidates_entries->whereIn('center_no', $center);
            }

            if (!is_null($subject)) {
                $candidates_entries =  $candidates_entries->whereIn('subject_code', $subject);
            }
            if (!is_null($year)) {
                $candidates_entries =  $candidates_entries->where('financial_year', '=',  $year);
            }

            if (!is_null($option)) {
                $candidates_entries =  $candidates_entries->where('subject_option', '=', $option);
            }
            $candidates_entries = $candidates_entries->limit(100)->get();
            $totalCandidates = 0;
            if (count($candidates_entries) > 0) {
                $output = "<table class='table table-condensed table-striped'>
            <thead>
                <tr>
                    <th>Center No</th>
                    <th>Center Name</th>
                    <th>District</th>
                    <th>Level</th>
                    <th>Session</th>
                    <th>Subject Code</th>
                    <th>Subject Name</th>
                    <th>Paper No</th>
                    <th>Paper Desc</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>";


                foreach ($candidates_entries as $center) {
                    $output .= "<tr>
                        <td>$center->center_no </td>
                        <td>$center->center_name </td>
                        <td>$center->district </td>
                        <td> $center->level </td>
                        <td> $center->session </td>
                        <td> $center->subject_code- $center->subject_option  </td>
                        <td> $center->subject_name </td>
                        <td> $center->paper_no </td>
                        <td> $center->pape_desc </td>
                        <td> $center->subject_number </td>
                    </tr>";
                    $totalCandidates = $totalCandidates + $center->subject_number;
                }

                $output  .= "<th colspan=6 class='heading'>Total Candiates</th>
            <th> $totalCandidates</th>";

                $output  .= " </tbody>
        </table>";
            } else {
                $output =  '<div>
                            No Candidates
                        </div>';
            }

            return response()->json(['cendidate_per_center' => $output, 'result' => $request->all()]);
        }
        $levels = Level::get();
        $sessions = Session::where('financial_year', $years[0])->get();

        return view('admin.entry.entry', compact('options', 'sponsors', 'years', 'levels', 'sessions'));
    }


    public function export(Request $request)
    {

        ini_set('memory_limit', '-1');
        set_time_limit(-1);
        $fileName = 'Reports ' . time() . '.csv';
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "public",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
        $level = $request->level;
        $session = $request->session;
        $center = $request->center;
        $subject = $request->subject;
        $year = $request->year;


        $candidates_entries = DB::table('centers_entries')
            ->select(
                [
                    'centers_entries.center_no',
                    'centers_entries.center_name',
                    'centers_entries.district',
                    'centers_entries.level',
                    'centers_entries.financial_year',
                    'centers_entries.session',
                    'centers_entries.subject_code',
                    'centers_entries.subject_option',
                    'centers_entries.subject_name',
                    'centers_entries.paper_no',
                    'centers_entries.pape_desc',
                    'centers_entries.subject_number',
                ],
            );
        if (!is_null($level)) {
            $candidates_entries =  $candidates_entries->where('level', '=', $level);
        }
        if (!is_null($session)) {
            $candidates_entries =  $candidates_entries->where('session', '=', $session);
        }
        if (!is_null($center)) {
            $candidates_entries = $candidates_entries->whereIn('center_no', $center);
        }

        if (!is_null($subject)) {
            $candidates_entries =  $candidates_entries->whereIn('subject_code', $subject);
        }
        if (!is_null($year)) {
            $candidates_entries =  $candidates_entries->where('financial_year', '=',  $year);
        }

        $candidates_entries = $candidates_entries->get();
        $columns = collect($candidates_entries->first())->keys()->all();

        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);
        foreach ($candidates_entries as $candidates_entrie) {
            $entries = array();
            foreach ($columns as  $column) {
                array_push($entries, $candidates_entrie->{$column});
            }
            fputcsv($file, $entries);
        }

        fclose($file);
        return Response::make('', 200, $headers);
    }




    public function autocompleteSearchCenter(Request $request)
    {
        $centers = [];
        if ($request->has('search')) {
            $center_name = $request->get('search');
            $centers = DB::table('centers_entries')
                ->select(
                    [
                        'centers_entries.center_no',
                        'centers_entries.center_name',
                        'centers_entries.district',
                        'centers_entries.level',
                        'centers_entries.financial_year',
                        'centers_entries.session'
                    ],
                );
            if (!empty($center_name)) {
                $centers = $centers->where('centers_entries.center_name', 'LIKE', "%{$center_name}%");
            }

            if (!empty($request->session)) {
                $centers = $centers->where('centers_entries.session', '=', $request->session);
            }
            if (!empty($request->level)) {
                $centers = $centers->where('centers_entries.level', '=', $request->level);
            }
            $centers = $centers->groupBy('centers_entries.center_no')->get();
            return response()->json($centers);
        }
    }


    public function autocompleteSearchSubject(Request $request)
    {
        $subjects = [];
        if ($request->has('search')) {
            $subject_name = trim($request->get('search'));
            $subjects = DB::table('centers_entries')
                ->select(
                    [
                        'centers_entries.subject_code',
                        'centers_entries.subject_option',
                        'centers_entries.subject_name',
                    ],
                );

            if (!empty($subject_name)) {
                $subjects = $subjects->where('centers_entries.subject_name', 'LIKE', "%{$subject_name}%");
            }

            if (!empty($request->session)) {
                $subjects = $subjects->where('centers_entries.session', '=', $request->session);
            }

            if (!empty($request->level)) {
                $subjects = $subjects->where('centers_entries.level', '=', $request->level);
            }

            $subjects = $subjects->groupBy('centers_entries.subject_code')->get();
            return response()->json($subjects);
        }
    }
}
