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
            if (isset($request->year)) {
                $levels =  $levels->where('center_candidate.financial_year', "=", $request->year);
                // Get Sessions
                $sessions = $sessions->where('center_candidate.financial_year', "=", $request->year);
                // Get Sponsors
                $sponsors = $sponsors->where('center_candidate.financial_year', "=", $request->year);
                // Get Centers
                $centers = $centers->where('center_candidate.financial_year', "=", $request->year);
                // Get Sponsors
                $sponsors = $sponsors->where('center_candidate.financial_year', "=", $request->year);
            }
            if (isset($request->level)) {
                $levels =  $levels->where('center_candidate.level', "=", $request->level);
                // Get Sessions
                $sessions = $sessions->where('center_candidate.level', "=", $request->level);
                // Get Centers
                $centers = $centers->where('center_candidate.level', "=", $request->level);
                // Get Sponsors
                $sponsors = $sponsors->where('center_candidate.level', "=", $request->level);
            }
            if (isset($request->session)) {
                // Get levels
                $levels = $levels->where('center_candidate.session', "=", $request->session);
                // Get Sessions
                $sessions = $sessions->where('center_candidate.session', "=", $request->session);
                // Get Centers
                $centers = $centers->where('center_candidate.session', "=", $request->session);
                // Get Sponsors
                $sponsors = $sponsors->where('center_candidate.session', "=", $request->session);
            }
            if (isset($request->center)) {
                $levels =  $levels->where('center_candidate.center_no', "=", $request->center);
                // Get Sessions
                $sessions = $sessions->where('center_candidate.center_no', "=", $request->center);
                // Get Centers
                $centers = $centers->where('center_candidate.center_no', "=", $request->center);
                // Get Sponsors
                $sponsors = $sponsors->where('center_candidate.center_no', "=", $request->center);
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
        $level = empty($request->level) ? 'LGCSE' : $request->level;
        $session = empty($request->session) ? 'June' : $request->session;
        $sponsor = $request->sponsor;
        $year = $request->year;
        $center = $request->center;
        $report = $request->report;
        if ($request->ajax()) {
            $fee_level = strtolower($level);
            $schoolFees = DB::table('fees_stracture')->select(
                [
                    'fees_stracture.candidate_type',
                    'fees_stracture.subject_fee',
                    'fees_stracture.registration_fee',
                    'fees_stracture.local_fee',
                    'fees_stracture.practical_subject_fee',
                    'fees_stracture.delf_fee',
                    'fees_stracture.bank_charge',
                    'fees_stracture.financial_year',
                ]
            )->join('sessions', function ($join) {
                $join->on('fees_stracture.session', '=', 'sessions.id');
                $join->on('fees_stracture.financial_year', '=', 'sessions.financial_year');
            })
                ->where('fees_stracture.candidate_type', '=', "$fee_level-school")
                ->where('sessions.session', '=', $session)
                ->where('fees_stracture.financial_year', '=', $year)
                ->first();
            $schoolPrivate = DB::table('fees_stracture')->select(
                [
                    'fees_stracture.candidate_type',
                    'fees_stracture.subject_fee',
                    'fees_stracture.registration_fee',
                    'fees_stracture.local_fee',
                    'fees_stracture.practical_subject_fee',
                    'fees_stracture.delf_fee',
                    'fees_stracture.bank_charge',
                    'fees_stracture.financial_year'
                ]
            )->join('sessions', function ($join) {
                $join->on('fees_stracture.session', '=', 'sessions.id');
                $join->on('fees_stracture.financial_year', '=', 'sessions.financial_year');
            })->where('fees_stracture.candidate_type', '=', "$fee_level-private")
                ->where('sessions.session', '=', $session)
                ->where('fees_stracture.financial_year', '=', $year)
                ->first();
            $practicalSubjects = ['0179', '0189', '0191', '0192', '0194', '0417', '0190'];

            $delf = Subject::whereHas('selectedDiscipline', function ($q) {
                $q->where('name', '=', 'LGCSE7');
            })->get()->pluck('subject_code')->toArray();


            $candidates =  DB::table('candidate_subject')
                ->select(
                    'centers.center_no',
                    'centers.center_name',
                    'center_candidate.id',
                    'center_candidate.candidate_no',
                    'center_candidate.national_id',
                    'center_candidate.type',
                    'center_candidate.subject_number',
                    'candidates.candidate_surname',
                    'candidates.candidate_other_name',
                    'candidates.date_of_birth',
                    'candidates.gender',
                    DB::raw("COALESCE( (SELECT SUM(invoices.amount) FROM invoices
                    WHERE invoices.client_id =  CONVERT( center_candidate.candidate_no USING UTF8MB4) COLLATE utf8mb4_unicode_ci and
                    invoices.session = center_candidate.session and
                    invoices.financial_year = center_candidate.financial_year
                    ),0) AS  amount_paid"),
                    'center_candidate.sponser',
                    DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code,' ',candidate_subject.subject_option)
                                order by candidate_subject.subject_code separator ',') as subjects")
                )
                ->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
                ->join('center_candidate', function ($join) {
                    $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                    $join->on('candidate_subject.level', '=', 'center_candidate.level');
                    $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
                    $join->on('candidate_subject.session', '=', 'center_candidate.session');
                })
                ->join('centers', 'centers.center_no', '=', 'center_candidate.center_no')
                ->groupBy('center_candidate.candidate_no',);

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
                ->orderBy('candidates.candidate_surname', "ASC");
            return DataTables::of($candidates)
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
                ->editColumn('price', function ($row) use ($schoolFees, $schoolPrivate, $practicalSubjects, $delf) {
                    $total_amount = 0;
                    $subjects = explode(",", $row->subjects);
                    if (in_array($row->type, [2, 3])) {
                        foreach ($subjects as $subject) {
                            $subject_code = explode(" ", $subject);
                            if (in_array($subject_code[0], $practicalSubjects)) {
                                $total_amount += ($schoolPrivate->subject_fee) + $schoolPrivate->practical_subject_fee;
                            } else if (in_array($subject, $delf)) {
                                $total_amount += ($schoolPrivate->delf_fee);
                            } else {
                                $total_amount += ($schoolPrivate->subject_fee);
                            }
                        }
                        $total_amount  += ($schoolPrivate->local_fee + $schoolPrivate->registration_fee);
                    } else {
                        foreach ($subjects as $subject) {
                            $subject_code = explode(" ", $subject);
                            if (in_array($subject_code[0], $practicalSubjects)) {
                                $total_amount += ($schoolFees->subject_fee) + $schoolFees->practical_subject_fee;
                            } else if (in_array($subject_code[0], $delf)) {
                                $total_amount += ($schoolFees->delf_fee);
                            } else {
                                $total_amount += ($schoolFees->subject_fee);
                            }
                        }
                        $total_amount  +=  $schoolFees->local_fee + $schoolFees->registration_fee;
                    }

                    return   "LSL " . number_format($total_amount, 2, '.', '');
                })->editColumn('amount_paid', function ($row) {
                    return   "LSL " . number_format($row->amount_paid, 2, '.', '');
                })
                ->editColumn('date_of_birth', function ($row) {
                    return $row->date_of_birth;
                })
                ->rawColumns(['candidate_no', 'national_id'])
                ->make(true);
        }
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
                $fee_level = strtolower($level);
                $schoolFees = DB::table('fees_stracture')->select(
                    [
                        'fees_stracture.candidate_type',
                        'fees_stracture.subject_fee',
                        'fees_stracture.registration_fee',
                        'fees_stracture.local_fee',
                        'fees_stracture.practical_subject_fee',
                        'fees_stracture.delf_fee',
                        'fees_stracture.bank_charge',
                        'fees_stracture.financial_year',
                    ]
                )->join('sessions', function ($join) {
                    $join->on('fees_stracture.session', '=', 'sessions.id');
                    $join->on('fees_stracture.financial_year', '=', 'sessions.financial_year');
                })
                    ->where('fees_stracture.candidate_type', '=', "$fee_level-school")
                    ->where('sessions.session', '=', $session)
                    ->where('fees_stracture.financial_year', '=', $year)
                    ->first();
                $schoolPrivate = DB::table('fees_stracture')->select(
                    [
                        'fees_stracture.candidate_type',
                        'fees_stracture.subject_fee',
                        'fees_stracture.registration_fee',
                        'fees_stracture.local_fee',
                        'fees_stracture.practical_subject_fee',
                        'fees_stracture.delf_fee',
                        'fees_stracture.bank_charge',
                        'fees_stracture.financial_year'
                    ]
                )->join('sessions', function ($join) {
                    $join->on('fees_stracture.session', '=', 'sessions.id');
                    $join->on('fees_stracture.financial_year', '=', 'sessions.financial_year');
                })->where('fees_stracture.candidate_type', '=', "$fee_level-private")
                    ->where('sessions.session', '=', $session)
                    ->where('fees_stracture.financial_year', '=', $year)
                    ->first();
                $practicalSubjects = ['0179', '0189', '0191', '0192', '0194', '0417', '0190'];

                $delf = Subject::whereHas('selectedDiscipline', function ($q) {
                    $q->where('name', '=', 'LGCSE7');
                })->get()->pluck('subject_code')->toArray();


                $columns =array('CENTRE NUMBER','CENTER NAME','CANDIDATE NUMBER','NATIONAL ID','CANDIDATE SURNAME','CANDIDATE OTHER NAMES','DATE OF BIRTH','GENDER','TYPE','TOTAL SUBJECTS','SPONSOR','TOTAL FEE','TOTAL AMOUNT PAID');
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);


                $candidates =  DB::table('candidate_subject')
                    ->select(
                        'centers.center_no',
                        'centers.center_name',
                        'center_candidate.id',
                        'center_candidate.candidate_no',
                        'center_candidate.national_id',
                        'center_candidate.type',
                        'center_candidate.subject_number',
                        'candidates.candidate_surname',
                        'candidates.candidate_other_name',
                        'candidates.date_of_birth',
                        DB::raw("COALESCE( (SELECT SUM(invoices.amount) FROM invoices  WHERE
                        invoices.client_id =  CONVERT( center_candidate.candidate_no USING UTF8MB4) COLLATE utf8mb4_unicode_ci and
                        invoices.session = center_candidate.session and
                        invoices.financial_year = center_candidate.financial_year
                        ),0) AS  amount_paid"),
                        'candidates.gender',
                        'center_candidate.sponser',
                        DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code,' ',candidate_subject.subject_option)
                                    order by candidate_subject.subject_code separator ',') as subjects")
                    )
                    ->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
                    ->join('center_candidate', function ($join) {
                        $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                        $join->on('candidate_subject.level', '=', 'center_candidate.level');
                        $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
                        $join->on('candidate_subject.session', '=', 'center_candidate.session');
                    })
                    ->join('centers', 'centers.center_no', '=', 'center_candidate.center_no')
                    ->groupBy('center_candidate.candidate_no');

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
                        $delf,
                        $practicalSubjects,
                        $schoolPrivate,
                        $schoolFees,
                        &$file,
                    ) {
                        $subjects = explode(",", $candidate->subjects);
                        $total_amount = 0;

                        if (in_array($candidate->type, [2, 3])) {
                            foreach ($subjects as $subject) {
                                if (in_array($subject, $practicalSubjects)) {

                                    $total_amount += $schoolPrivate->subject_fee + $schoolPrivate->practical_subject_fee;
                                } else if (in_array($subject, $delf)) {

                                    $total_amount += ($schoolPrivate->delf_fee);
                                } else {

                                    $total_amount += $schoolPrivate->subject_fee;
                                }
                            }
                            $total_amount  +=  $schoolPrivate->local_fee + $schoolPrivate->registration_fee;

                        } else {
                            foreach ($subjects as $subject) {
                                if (in_array($subject, $practicalSubjects)) {

                                    $total_amount += $schoolFees->subject_fee + $schoolFees->practical_subject_fee;
                                } else if (in_array($subject, $delf)) {

                                    $total_amount += ($schoolFees->delf_fee);
                                } else {

                                    $total_amount += $schoolFees->subject_fee;
                                }
                            }


                            $total_amount  +=  $schoolFees->local_fee + $schoolFees->registration_fee;

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
                         $candidate->amount_paid));
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


                $funders = $funders->whereIn('sponsor',$sponsors)
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
                    if(!next($funders ) ) {
                        $funder_headers  .=",";
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
                            // $sponser_total_candidate = $sponsors[$funder->sponsor]['sponser_total_candidate'];
                            // $sponsor_overdue = $sponsors[$funder->sponsor]['sponsor_overdue'];
                            // $total_amount_paid = $sponsors[$funder->sponsor]['total_amount_paid'];
                            // $total_subjects = $sponsors[$funder->sponsor]['total_subjects'];
                            // $practical = $sponsors[$funder->sponsor]['practical'];
                            // $delf = $sponsors[$funder->sponsor]['delf'];
                        }else{
                            $sponsors_results  .= implode(",", $sponsors_array);
                        }
                        if(!next($funders ) ) {
                            $sponsors_results .=",";
                        }

                    }

                    $other_sponsor_total =isset($schoolfees['sponsors']['O']['sponsor_overdue'])?$schoolfees['sponsors']['O']['sponsor_overdue']:0;  ;
                    $other_charges=$center_result['other_charges'];
                    $total_paid=$center_result['total_paid'];
                    $balance =  $other_sponsor_total +  $other_charges- $total_paid;
                    $center_results .= $sponsors_results;








                    // $totalOverDue = $centersFee['bank_charge'] + $centersFee['total_charge'] + $centersFee['sponsor'][2];
                    // $totalPaid = $centersFee['total_paid'];
                    // $balance = $totalOverDue - $totalPaid;
                    // $totalAmount = $centersFee['total_amount'] + $centersFee['total_charge'];


                    fputcsv($file, explode(',', $center_results));


                    // fputcsv($file, array(
                    //     $center->center_no,
                    //     $center->center_name,
                    //     $center_result['total_candidates'],
                    //     $sponser_total_candidate,
                    //     $sponsors_results,
                    //     // number_format($centersFee['sponsor'][1], 2, '.', ''),
                    //     // number_format($centersFee['sponsor'][2], 2, '.', ''),
                    //     // number_format($centersFee['bank_charge'], 2, '.', ''),
                    //     // number_format($centersFee['total_charge'], 2, '.', ''),
                    //     // number_format($totalOverDue, 2, '.', ''),
                    //     // number_format($centersFee['total_paid'], 2, '.', ''),
                    //     // number_format($balance, 2, '.', ''),
                    //     // number_format($totalAmount, 2, '.', '')
                    // ));
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
