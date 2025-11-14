<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libraries\Payment\Payment;
use App\Models\Center;
use App\Models\CenterCandidate;
use App\Models\Session;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use PhpParser\Node\Stmt\Echo_;
use Yajra\DataTables\Facades\DataTables;

class FinantialReportController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $level =  $request->level;
            $session = $request->session;
            $sponsor = $request->sponsor;
            $year = $request->year;
            $center = $request->center;
            if ($request->has('filters')) {
                $levels = DB::table('center_candidate')->select(
                    [
                        'center_candidate.level'
                    ],
                );
                // Get Sessions
                $sessions = DB::table('center_candidate')->select(
                    [
                        'center_candidate.session'
                    ],
                );
                // Get Centers
                $centers = DB::table('center_candidate')
                    ->select(
                        'centers.level',
                        'centers.center_name',
                        'center_candidate.center_no'
                    )->join('centers', 'center_candidate.center_no', '=', 'centers.center_no');
                // Get Sponsors
                $sponsors = DB::table('center_candidate')->select(
                    [
                        'center_candidate.sponser'
                    ],
                );
                if (isset($year)) {
                    $levels =  $levels->where('center_candidate.financial_year', "=", $year);
                    // Get Sessions
                    $sessions = $sessions->where('center_candidate.financial_year', "=", $year);
                    // Get Sponsors
                    $sponsors = $sponsors->where('center_candidate.financial_year', "=", $year);
                    // Get Centers
                    $centers = $centers->where('center_candidate.financial_year', "=", $year);
                    // Get Sponsors
                    $sponsors = $sponsors->where('center_candidate.financial_year', "=", $year);
                }
                if (isset($level)) {
                    $levels =  $levels->where('center_candidate.level', "=", $level);
                    // Get Sessions
                    $sessions = $sessions->where('center_candidate.level', "=", $level);
                    // Get Centers
                    $centers = $centers->where('center_candidate.level', "=", $level);
                    // Get Sponsors
                    $sponsors = $sponsors->where('center_candidate.level', "=", $level);
                }
                if (isset($session)) {
                    // Get levels
                    $levels = $levels->where('center_candidate.session', "=", $session);
                    // Get Sessions
                    $sessions = $sessions->where('center_candidate.session', "=", $session);
                    // Get Centers
                    $centers = $centers->where('center_candidate.session', "=", $session);
                    // Get Sponsors
                    $sponsors = $sponsors->where('center_candidate.session', "=", $session);
                }
                if (isset($center)) {
                    $levels =  $levels->where('center_candidate.center_no', "=", $center);
                    // Get Sessions
                    $sessions = $sessions->where('center_candidate.center_no', "=", $center);
                    // Get Centers
                    $centers = $centers->where('center_candidate.center_no', "=", $center);
                    // Get Sponsors
                    $sponsors = $sponsors->where('center_candidate.center_no', "=", $center);
                }
                $levels = $levels->distinct()
                    ->orderBy('level')
                    ->get()->pluck('level')->toArray();
                // Get Sessions
                $sessions = $sessions->distinct()
                    ->orderBy('session')
                    ->get()->pluck('session')->toArray();
                // Get Centers
                $centers = $centers->orderBy('center_candidate.center_no', 'ASC')
                    ->distinct()
                    ->groupBy(['center_candidate.center_no'])
                    ->get()->pluck('center_name', 'center_no')->toArray();
                // Get Sponsors
                $sponsors =  $sponsors->distinct()
                    ->orderBy('sponser')
                    ->get()->pluck('sponser')->toArray();
                return response()->json(['levels' =>  $levels, 'sessions' => $sessions, 'centers' => $centers, 'sponsors' => $sponsors]);
            }
            $candidates = DB::table('centers')
                ->select(
                    'centers.center_no',
                    'centers.center_name',
                    'candidates.candidate_no',
                    'center_candidate.national_id',
                    'candidates.candidate_other_name',
                    'candidates.candidate_surname',
                    'candidates.gender',
                    'candidates.date_of_birth',
                    'center_candidate.id',
                    'center_candidate.level',
                    'center_candidate.financial_year',
                    'center_candidate.session',
                    DB::raw("COALESCE( (SELECT SUM(fee_candidate_histories.amount) FROM fee_candidate_histories  WHERE
                 fee_candidate_histories.candidate_id = center_candidate.id
                 and fee_candidate_histories.status='1'
                 ),0) AS amount_paid"),
                 DB::raw("COALESCE( (SELECT SUM(fee_candidate_histories.fine) FROM fee_candidate_histories  WHERE
                 fee_candidate_histories.candidate_id = center_candidate.id
                 and fee_candidate_histories.status='1'
                 ),0) AS fine"),
                    DB::raw("COALESCE((Select sum(fee_group_details.amount) from fee_groups
                         inner join fee_group_details on  fee_group_details.fee_group_id=fee_groups.id
                         inner join fee_types on  fee_types.id=fee_group_details.fee_type_id
                         inner join sessions on  sessions.id=fee_groups.session_id
                         inner join levels on  levels.id=fee_groups.level_id
                         where fee_groups.candidate_type=CASE center_candidate.type
                                                     WHEN 1 THEN 1
                                                     WHEN 2 THEN 3
                                                     ELSE 3
                                                     END
                         and sessions.session=center_candidate.session and
                          levels.level=center_candidate.level and
                          sessions.financial_year=center_candidate.financial_year
                         and  fee_group_details.subject_code in (
                         (SELECT  candidate_subject.subject_code  FROM `candidate_subject`
                         WHERE `candidate_subject`.`candidate_no`=`center_candidate`.`candidate_no` AND
                             `candidate_subject`.`level`=`center_candidate`.`level` AND
                             `candidate_subject`.`session`=`center_candidate`.`session` AND
                             `candidate_subject`.`financial_year`=`center_candidate`.`financial_year`
                             union SELECT '-' as subject_code)
                         )),0) as price")
                )
                ->join('center_candidate', 'center_candidate.center_no', '=', 'centers.center_no')
                ->join('candidates', 'candidates.candidate_no', '=', 'center_candidate.candidate_no');

            if (!empty($center)) {
                $candidates = $candidates->where('centers.center_no', "=", $center);
            }
            if (!empty($year)) {
                $candidates = $candidates->where('center_candidate.financial_year', "=", $year);
            }
            if (!empty($level)) {
                $candidates = $candidates->where('center_candidate.level', "=", $level);
            }
            if (!empty($sponsor)) {
                $candidates = $candidates->where('center_candidate.sponser', "=", $sponsor);
            }
            if (!empty($session)) {
                $candidates = $candidates->where('center_candidate.session', "=", $session);
            }
            $candidates = $candidates;
            return DataTables::of($candidates)
                ->setRowId('candidate_no')
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-url="' . route('admin.fees-stracture.fee-histories.show', $row->id)  . '" data-original-title="collect" class="view-history btn btn-primary"><i class="fa fa-arrow-right" aria-hidden="true">Collect</i></a>';
                    return $btn;
                })
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
                ->editColumn('amount_paid', function ($row) {
                    return   "LSL " . number_format($row->amount_paid, 2, '.', '');
                })
                ->editColumn('date_of_birth', function ($row) {
                    return $row->date_of_birth;
                })
                ->rawColumns(['candidate_no', 'national_id', 'action'])
                ->make();
        }




        $years =  CenterCandidate::select(DB::raw('financial_year as year'))
            ->orderBy('year', 'DESC')
            ->distinct()
            ->get()->pluck('year');
        return view('admin.finance.reports.index', compact('years'));
    }

    public function report(Request $request)
    {

        ini_set('memory_limit', '-1');
        set_time_limit(-1);
        $level = $request->level;
        $session =$request->session;
        $sponsor = $request->sponsor;
        $year = $request->year;
        $center = $request->center;
        $report = $request->report;


        switch ($report) {
            case '1':
                $fileName = "Sponser Reports $year" . time() . '.csv';
                $headers = array(
                    "Content-type"        => "text/csv",
                    "Content-Disposition" => "attachment; filename=$fileName",
                    "Pragma"              => "no-cache",
                    "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                    "Expires"             => "0"
                );

                $columns = array('CENTRE NUMBER', 'CENTER NAME', 'CANDIDATE NUMBER', 'NATIONAL ID', 'CANDIDATE SURNAME', 'CANDIDATE OTHER NAMES', 'DATE OF BIRTH', 'GENDER', 'TYPE', 'TOTAL SUBJECTS', 'SPONSOR', 'TOTAL FEE','FINE', 'TOTAL AMOUNT PAID', 'BALANCE', );
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                $candidates = DB::table('centers')
                    ->select(
                        'centers.center_no',
                        'centers.center_name',
                        'candidates.candidate_no',
                        'center_candidate.national_id',
                        'candidates.candidate_other_name',
                        'candidates.candidate_surname',
                        'candidates.gender',
                        'candidates.date_of_birth',
                        'center_candidate.id',
                        'center_candidate.type',
                        'center_candidate.subject_number',
                        'center_candidate.sponser',
                        'center_candidate.level',
                        'center_candidate.financial_year',
                        'center_candidate.session',
                        DB::raw("COALESCE( (SELECT SUM(fee_candidate_histories.amount) FROM fee_candidate_histories  WHERE
                 fee_candidate_histories.candidate_id = center_candidate.id
                 and fee_candidate_histories.status='1'
                 GROUP BY  fee_candidate_histories.candidate_id

                 ),0) AS amount_paid"),
                        DB::raw("COALESCE( (SELECT SUM(fee_candidate_histories.fine) FROM fee_candidate_histories  WHERE
                 fee_candidate_histories.candidate_id = center_candidate.id
                 and fee_candidate_histories.status='1'
                  GROUP BY  fee_candidate_histories.candidate_id
                 ),0) AS fine"),
                        DB::raw("COALESCE((Select sum(fee_group_details.amount) from fee_groups
                         inner join fee_group_details on  fee_group_details.fee_group_id=fee_groups.id
                         inner join fee_types on  fee_types.id=fee_group_details.fee_type_id
                         inner join sessions on  sessions.id=fee_groups.session_id
                         inner join levels on  levels.id=fee_groups.level_id
                         where fee_groups.candidate_type=CASE center_candidate.type
                                                     WHEN 1 THEN 1
                                                     WHEN 2 THEN 3
                                                     ELSE 3
                                                     END
                         and sessions.session=center_candidate.session and
                          levels.level=center_candidate.level and
                          sessions.financial_year=center_candidate.financial_year
                         and  fee_group_details.subject_code in (
                         (SELECT  candidate_subject.subject_code  FROM `candidate_subject`
                         WHERE `candidate_subject`.`candidate_no`=`center_candidate`.`candidate_no` AND
                             `candidate_subject`.`level`=`center_candidate`.`level` AND
                             `candidate_subject`.`session`=`center_candidate`.`session` AND
                             `candidate_subject`.`financial_year`=`center_candidate`.`financial_year`
                             union SELECT '-' as subject_code)
                         )),0) as price")
                    )
                    ->join('center_candidate', 'center_candidate.center_no', '=', 'centers.center_no')
                    ->join('candidates', 'candidates.candidate_no', '=', 'center_candidate.candidate_no');


                if (!empty($center)) {
                    $candidates = $candidates->where('centers.center_no', "=", $center);
                }
                if (!empty($year)) {
                    $candidates = $candidates->where('center_candidate.financial_year', "=", $year);
                }
                if (!empty($level)) {
                    $candidates = $candidates->where('center_candidate.level', "=", $level);
                }
                if (!empty($sponsor)) {
                    $candidates = $candidates->where('center_candidate.sponser', "=", $sponsor);
                }
                if (!empty($session)) {
                    $candidates = $candidates->where('center_candidate.session', "=", $session);
                }

                $candidates =  $candidates->where('center_candidate.financial_year', '=', $year)
                    ->orderBy('centers.center_no', 'ASC')
                    ->orderBy('candidates.candidate_surname', "ASC")
                    ->each(function (object $candidate) use (
                        &$file,
                    ) {
                        $total_amount = $candidate->price + $candidate->fine;
                        $candidate_balance = $total_amount - $candidate->amount_paid;

                        if ($candidate_balance < 0) {
                            $balance=abs($candidate_balance);

                            // DB::statement("UPDATE fee_candidate_histories SET fine = $balance WHERE candidate_id =$candidate->id");
                        }



                        fputcsv($file, array(
                            $candidate->center_no,
                            $candidate->center_name,
                            $candidate->candidate_no,
                            $candidate->national_id,
                            $candidate->candidate_surname,
                            $candidate->candidate_other_name,
                            $candidate->date_of_birth,
                            $candidate->gender,
                            $candidate->type,
                            $candidate->subject_number,
                            $candidate->sponser,
                            $total_amount,
                            $candidate->fine,
                            $candidate->amount_paid,
                            $candidate_balance,

                        ));
                    });
                fclose($file);
                return Response::make('', 200, $headers);
                break;
            case '2':
                $centers = DB::table('center_candidate')
                    ->select(
                        'center_candidate.level',
                        'centers.center_name',
                        'center_candidate.center_no',
                        DB::raw("group_concat(DISTINCT center_candidate.sponser order by sponser separator ',') as sponser"),
                        DB::raw("group_concat(DISTINCT center_candidate.session order by session separator ',') as session")
                    )
                    ->join('centers', 'center_candidate.center_no', '=', 'centers.center_no');

                $funders = DB::table('funders')
                    ->select(
                        'sponsor',
                        'name',
                        'decription'
                    );

                if (isset($center)) {
                    $centers = $centers->where('centers.center_no', "=", $center);
                }
                if (isset($year)) {
                    $centers = $centers->where('center_candidate.financial_year', "=", $year);
                }
                if (isset($level)) {
                    $centers = $centers->where('center_candidate.level', "=", $level);
                }
                if (isset($sponsor)) {
                    $centers = $centers->where('center_candidate.sponser', "=", $sponsor);
                    $funders =  $funders->where('sponsor', "=", $sponsor);
                }
                if (isset($session)) {
                    $centers = $centers->where('center_candidate.session', "=", $session);
                }



                $centers = $centers->orderBy('center_candidate.center_no', 'ASC')
                    ->distinct()
                    ->groupBy(['center_candidate.center_no'])


                    ->get();

                $sponsors = DB::table('center_candidate')->select(
                    [
                        'center_candidate.sponser'
                    ],
                )->distinct()
                    ->orderBy('sponser')
                    ->where('center_candidate.financial_year', '=', $year)
                    ->where('center_candidate.session', '=', $session)
                    ->where('center_candidate.level', '=', $level)
                    ->get()->pluck('sponser')->toArray();


                $funders = $funders->whereIn('sponsor', $sponsors)
                    ->orderBy('sponsor')->get();



                $fileName = "Sponser Reports $year" . time() . '.csv';
                $headers = array(
                    "Content-type"        => "text/csv",
                    "Content-Disposition" => "attachment; filename=$fileName",
                    "Pragma"              => "no-cache",
                    "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                    "Expires"             => "0"
                );
                // columns Title
                $funders->map(function ($table) {
                    $table->totals = "$table->name total_candidate,$table->name sponsor_overdue,$table->name total_amount_paid,$table->name total_subjects,$table->name practical,$table->name delf";
                    return $table;
                });
                $funder_headers = "Centre Number,Centre Name,#.Candidates,";
                foreach ($funders->pluck('totals')->toArray() as $funder) {
                    $funder_headers .= $funder;
                    if (!next($funders)) {
                        $funder_headers  .= ",";
                    }
                }

                $columns = explode(',', $funder_headers);
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                foreach ($centers  as  $center) {
                    $center_session = $center->session;
                    $center_level = $center->level;
                    $center_no = $center->center_no;
                    $center_result = Payment::schoolfees($center_no, $center_level,  $center_session, $year);
                    $sponsors = $center_result['sponsors'];
                    $total_candidates = $center_result['total_candidates'];
                    $sponsors_results = "";
                    $sponsors_array = array(
                        "total_candidate" => 0,
                        "sponsor_overdue" => 0,
                        "total_amount_paid" => 0,
                        "total_subjects" => 0,
                        "practical" => 0,
                        "delf" => 0,
                    );
                    $center_results = "$center->center_no,$center->center_name,$total_candidates,";
                    foreach ($funders as $funder) {
                        if (isset($sponsors[$funder->sponsor])) {
                            $sponsors_array = $sponsors[$funder->sponsor];
                            $sponsors_results  .= implode(",", $sponsors_array);
                        } else {
                            $sponsors_results  .= implode(",", $sponsors_array);
                        }
                        if (!next($funders)) {
                            $sponsors_results .= ",";
                        }
                    }

                    $other_sponsor_total = isset($schoolfees['sponsors']['O']['sponsor_overdue']) ? $schoolfees['sponsors']['O']['sponsor_overdue'] : 0;;
                    $other_charges = $center_result['other_charges'];
                    $total_paid = $center_result['total_paid'];
                    $balance =  $other_sponsor_total +  $other_charges - $total_paid;
                    $center_results .= $sponsors_results;

                    fputcsv($file, explode(',', $center_results));
                }


                fclose($file);
                return Response::make('', 200, $headers);
                break;
            default:
                # code...
                break;
        }
    }
}
