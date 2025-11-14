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



        if ($request->ajax()) {
            $level = $request->level;
            $session = $request->session;
            $center = $request->center;
            $subject = $request->subject;
            $year = $request->year;
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

                $output  .= "</tbody>
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

        return view('admin.entry.entry', compact('years', 'levels', 'sessions'));
    }


    public function export(Request $request)
    {

        ini_set('memory_limit', '-1');
        set_time_limit(-1);
        $fileName = 'Entries_' . date('Y-m-d H:i:s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];


        $entry = $request->entry;

        switch ($entry) {
            case 'entries':
                return response()->streamDownload(function () use ($request) {
                    $level = $request->level;
                    $session = $request->session;
                    $center = $request->center;
                    $subject = $request->subject;
                    $year = $request->year;

                    $candidates_entries = DB::table('centers_entries')
                        ->select(
                            [
                                'centers_entries.center_no',
                                'centers_entries.center_full_name',
                                'centers_entries.center_name',
                                'centers_entries.address',
                                'centers_entries.district',
                                'centers_entries.district_code',
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



                    $candidates_entries = $candidates_entries->orderBy('center_no', 'ASC')
                        ->orderBy('subject_code', "ASC");
                    // Create file pointer (output stream)
                    $handle = fopen('php://output', 'w');

                    // Add CSV headers
                    $columns = collect($candidates_entries->first())->keys()->all();
                    fputcsv($handle, $columns);

                    $candidates_entries->each(function (object $candidates_entrie) use (
                        &$handle,
                        &$columns,
                    ) {
                        $entries = array();
                        foreach ($columns as  $column) {
                            array_push($entries, $candidates_entrie->{$column});
                        }
                        fputcsv($handle, $entries);
                    });
                    fclose($handle);
                },   $fileName, $headers);
                break;
            case 'entries_kingdom':
                return response()->streamDownload(function () use ($request) {
                    $level = $request->level;
                    $session = $request->session;
                    $center = $request->center;
                    $year = $request->year;
                    $candidates_entries = DB::table('center_candidate')
                        ->select(
                            'center_candidate.center_no',
                            'centers.center_full_name',
                            'centers.center_name',
                            'centers.district',
                            'centers.district_code',
                            'centers.address',
                            'center_candidate.level',
                            'center_candidate.session',
                            'center_candidate.financial_year',
                            DB::raw("group_concat(DISTINCT concat(center_candidate.sponser)) as sponsors"),
                            DB::raw("count(center_candidate.candidate_no) as candidates")
                        )
                        ->join('centers', 'center_candidate.center_no', '=', 'centers.center_no');
                        // ->groupBy('center_candidate.financial_year', 'center_candidate.level', 'center_candidate.session', 'center_candidate.center_no')
                        // ->get();


                    if (!is_null($level)) {
                        $candidates_entries =  $candidates_entries->where('center_candidate.level', '=', $level);
                    }
                    if (!is_null($session)) {
                        $candidates_entries =  $candidates_entries->where('center_candidate.session', '=', $session);
                    }
                    if (!is_null($center)) {
                        $candidates_entries = $candidates_entries->whereIn('center_candidate.center_no', $center);
                    }


                    if (!is_null($year)) {
                        $candidates_entries =  $candidates_entries->where('center_candidate.financial_year', '=',  $year);
                    }


                    $candidates_entries = $candidates_entries->groupBy(
                        'center_candidate.center_no',
                        'center_candidate.financial_year',
                        'center_candidate.level',
                        'center_candidate.session',
                    )->orderBy('center_candidate.center_no', 'ASC');
                    // Create file pointer (output stream)
                    $handle = fopen('php://output', 'w');

                    // Add CSV headers
                    $columns = collect($candidates_entries->first())->keys()->all();
                    fputcsv($handle, $columns);

                    $candidates_entries->each(function (object $candidates_entrie) use (
                        &$handle,
                        &$columns,
                    ) {
                        $entries = array();
                        foreach ($columns as  $column) {
                            array_push($entries, $candidates_entrie->{$column});
                        }
                        fputcsv($handle, $entries);
                    });
                    fclose($handle);
                },   $fileName, $headers);
                break;
            case 'entries_paid':
                return response()->streamDownload(function () use ($request) {
                    $level = $request->level;
                    $session = $request->session;
                    $center = $request->center;
                    $subject = $request->subject;
                    $year = $request->year;
                    $candidates_entries = DB::table('center_entries_paid')
                        ->select(
                            [
                                'center_no',
                                'center_full_name',
                                'center_name',
                                'address',
                                'district',
                                'district_code',
                                'level',
                                'financial_year',
                                'session',
                                'subject_code',
                                'subject_option',
                                'subject_name',
                                'paper_no',
                                'pape_desc',
                                'subject_number',
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



                    $candidates_entries = $candidates_entries->orderBy('center_no', 'ASC')
                        ->orderBy('subject_code', "ASC");
                    // Create file pointer (output stream)
                    $handle = fopen('php://output', 'w');

                    // Add CSV headers
                    $columns = collect($candidates_entries->first())->keys()->all();
                    fputcsv($handle, $columns);

                    $candidates_entries->each(function (object $candidates_entrie) use (
                        &$handle,
                        &$columns,
                    ) {
                        $entries = array();
                        foreach ($columns as  $column) {
                            array_push($entries, $candidates_entrie->{$column});
                        }
                        fputcsv($handle, $entries);
                    });
                    fclose($handle);
                },   $fileName, $headers);
                break;

            default:
                # code...
                break;
        }
    }




    public function autocompleteSearchCenter(Request $request)
    {
        $centers = [];
        if ($request->has('search')) {
            $center_name = $request->get('search');
            $centers = DB::table('center_candidate')
                ->select(
                    [
                        'center_candidate.center_no',
                        'centers.center_name',
                        'centers.district',
                    ],
                )->join('candidate_subject', 'candidate_subject.candidate_no', '=', 'center_candidate.candidate_no')
                ->join('centers', 'center_candidate.center_no', '=', 'centers.center_no');



            if (!empty($center_name)) {
                $centers = $centers->where('centers.center_name', 'LIKE', "%{$center_name}%");
            }
            if (!empty($request->session)) {
                $centers = $centers->where('center_candidate.session', '=', $request->session);
            }
            if (!empty($request->level)) {
                $centers = $centers->where('center_candidate.level', '=', $request->level);
            }
            $centers = $centers->groupBy('center_candidate.center_no')->get();
            return response()->json($centers);
        }
    }


    public function autocompleteSearchSubject(Request $request)
    {
        $subjects = [];
        if ($request->has('search')) {
            $subject_name = trim($request->get('search'));
            $subjects = DB::table('subjects')
                ->select(
                    [
                        'subjects.subject_code',
                        'subjects.subject_name',
                    ],
                )
                ->join('levels', 'subjects.level', '=', 'levels.id');

            if (!empty($subject_name)) {
                $subjects = $subjects->where('subjects.subject_name', 'LIKE', "%{$subject_name}%");
            }



            if (!empty($request->level)) {
                $subjects = $subjects->where('levels.level', '=', $request->level);
            }

            $subjects = $subjects->groupBy('subjects.subject_code')->get();
            return response()->json($subjects);
        }
    }
}
