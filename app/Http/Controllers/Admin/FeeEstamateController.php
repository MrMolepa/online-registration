<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libraries\Payment\Payment;
use App\Models\Center;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class FeeEstamateController extends Controller
{
    public function index(Request $request)
    {

        $financial_year = (date('m') <= 4) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        if ($request->ajax()) {
            $centers = Center::query()->whereHas('candidates', function ($query) use ($request, $financial_year) {
                $query->where('financial_year', "=", $financial_year)
                    ->whereIn('sponser', ['O', 'N', 'M']);
            });
            return   DataTables::eloquent($centers)
                ->addColumn('actions', function ($model) {
                    $actions = '<div class="btn-group actions">';
                    $actions .= "<a href='" . route('admin.payments-verification.center', $model->center_no) . "'
                                data-toggle='tooltip' title='Proof of payments'
                                class='btn btn-primary'><i class='fas fa-file-invoice-dollar'></i> 
                             </a>";

                    $actions .= '</div>';
                    return       $actions;
                })->rawColumns(['actions'])
                ->toJson();
        }
        try {

            $schoolResult = Payment::schoolfees(null, null, null, $financial_year);
            $nmds = $schoolResult['sponsor'][0];
            $mosd = $schoolResult['sponsor'][1];
            $other = $schoolResult['sponsor'][2];
            $other_private = $schoolResult['sponsor'][3];
            $other  = $other + $other_private;
            $total_sum = $nmds + $mosd + $other;
            return view('admin.finance.fee-estamates.fee-estamates', compact('nmds', 'mosd', 'other', 'total_sum'));
        } catch (\Exception $e) {
            dd($e);
        }
    }


    public function privateCenters(Request $request)
    {
        $financial_year = (date('m') <= 4) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        if ($request->ajax()) {
            $privateCenters = Center::query()->whereHas('candidates', function ($query) use ($request, $financial_year) {
                $query->where('financial_year', '=', $financial_year)
                    ->whereNotIn('sponser', ['O', 'N', 'M']);
            });
            return   DataTables::eloquent($privateCenters)
                ->toJson();
        }
    }


    public function sponsorReport()
    {


        //all sponsors

        $payment = new Payment();
        // $schoolResultFee = $payment->schoolfees();

        $fileName = 'Reports ' . time() . '.csv';
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );


        $schoolFees = DB::table('fees_stracture')->where('id', '=', 1)->first();
        $schoolPrivate = DB::table('fees_stracture')->where('id', '=', 2)->first();

        
        $practicalSubjects = ['0179', '0189', '0191', '0192', '0194', '0417',];
        $sponsor_N =  DB::table('candidate_subject')
            ->select(
                'centers.center_no',
                'centers.center_name',
                'center_candidate.candidate_no',
                'center_candidate.type',
                'center_candidate.subject_number',
                'candidates.candidate_surname',
                'candidates.candidate_other_name',
                'candidates.date_of_birth',
                'center_candidate.sponser',
                DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code,' ',candidate_subject.subject_option)  
            order by candidate_subject.subject_code separator ',') as subjects")
            )

            ->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('center_candidate', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
            })
            // ->join('center_candidate', 'center_candidate.candidate_no', '=', 'candidate_subject.candidate_no')
            ->leftJoin('centers', 'centers.center_no', '=', 'center_candidate.center_no')
            ->groupBy('center_candidate.candidate_no')
            ->where('center_candidate.type', '=', "1")
            ->where('center_candidate.sponser', '=', "N")
            ->orderBy('centers.center_no', 'ASC')
            ->orderBy('candidates.candidate_surname', "ASC")
            ->get();

        $sponsor_O =  DB::table('candidate_subject')
            ->select(
                'centers.center_no',
                'centers.center_name',
                'center_candidate.candidate_no',
                'center_candidate.type',
                'center_candidate.subject_number',
                'candidates.candidate_surname',
                'candidates.candidate_other_name',
                'candidates.date_of_birth',
                'center_candidate.sponser',
                DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code,' ',candidate_subject.subject_option)  
            order by candidate_subject.subject_code separator ',') as subjects")
            )
            ->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('center_candidate', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
            })
            // ->join('center_candidate', 'center_candidate.candidate_no', '=', 'candidate_subject.candidate_no')
            ->leftJoin('centers', 'centers.center_no', '=', 'center_candidate.center_no')
            ->groupBy('center_candidate.candidate_no')
            ->where('center_candidate.type', '=', "1")
            ->where('center_candidate.sponser', '=', "O")
            ->orderBy('centers.center_no', 'ASC')
            ->orderBy('candidates.candidate_surname', "ASC")
            ->get();
        // For Type 3 and 2
        $sponsor_O_type =  DB::table('candidate_subject')
            ->select(
                'centers.center_no',
                'centers.center_name',
                'center_candidate.candidate_no',
                'center_candidate.type',
                'center_candidate.subject_number',
                'candidates.candidate_surname',
                'candidates.candidate_other_name',
                'candidates.date_of_birth',
                'center_candidate.sponser',
                DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code,' ',candidate_subject.subject_option)  
            order by candidate_subject.subject_code separator ',') as subjects")
            )
            ->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('center_candidate', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
            })
            // ->join('center_candidate', 'center_candidate.candidate_no', '=', 'candidate_subject.candidate_no')
            ->leftJoin('centers', 'centers.center_no', '=', 'center_candidate.center_no')
            ->groupBy('center_candidate.candidate_no')
            ->whereIn('center_candidate.type', [2, 3])
            ->where('center_candidate.sponser', '=', "O")
            ->orderBy('candidates.candidate_surname', "ASC")
            ->get();




        $sponsor_M =  DB::table('candidate_subject')
            ->select(
                'centers.center_no',
                'centers.center_name',
                'center_candidate.candidate_no',
                'center_candidate.type',
                'center_candidate.subject_number',
                'candidates.candidate_surname',
                'candidates.candidate_other_name',
                'candidates.date_of_birth',
                'center_candidate.sponser',
                DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code,' ',candidate_subject.subject_option)  
            order by candidate_subject.subject_code separator ',') as subjects")
            )
            ->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('center_candidate', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
            })
            // ->join('center_candidate', 'center_candidate.candidate_no', '=', 'candidate_subject.candidate_no')
            ->leftJoin('centers', 'centers.center_no', '=', 'center_candidate.center_no')
            ->groupBy('center_candidate.candidate_no')
            ->where('center_candidate.type', '=', "1")
            ->where('center_candidate.sponser', '=', "M")
            ->orderBy('candidates.candidate_surname', "ASC")
            ->get();


        $columns = array(' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ');
        $callback = function () use ($sponsor_O_type, $sponsor_O, $sponsor_N, $sponsor_M, $columns, $payment, $schoolFees, $schoolPrivate, $practicalSubjects) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            $sponsor_O_total = 0;
            if ($sponsor_O || $sponsor_O_type) {
                if ($sponsor_O) {
                    foreach ($sponsor_O  as $value) {
                        $candidate_total =  0;
                        $candidate_total +=  $schoolFees->local_fee + $schoolFees->registration_fee;
                        $subjects = explode(",", $value->subjects);
                        foreach ($subjects as $subject) {
                            $subject_code = explode(" ", $subject);
                            if (in_array($subject_code[0], $practicalSubjects)) {
                                $candidate_total += ($schoolFees->subject_fee) + $schoolFees->practical_subject_fee;
                            } else {
                                $candidate_total += ($schoolFees->subject_fee);
                            }
                        }
                        fputcsv($file, array(
                            $value->center_no,
                            $value->center_name,
                            $value->candidate_no,
                            $value->candidate_other_name,
                            $value->candidate_surname,
                            number_format($candidate_total, 2, '.', ''),
                            $value->sponser
                        ));

                        $sponsor_O_total +=  $candidate_total;
                    }
                }
                if ($sponsor_O_type) {
                    foreach ($sponsor_O_type  as $value) {
                        $candidate_total =  0;
                        $candidate_total +=  $schoolPrivate->local_fee + $schoolPrivate->registration_fee;
                        $subjects = explode(",", $value->subjects);
                        foreach ($subjects as $subject) {
                            $subject_code = explode(" ", $subject);
                            if (in_array($subject_code[0], $practicalSubjects)) {
                                $candidate_total += ($schoolPrivate->subject_fee) + $schoolPrivate->practical_subject_fee;
                            } else {
                                $candidate_total += ($schoolPrivate->subject_fee);
                            }
                        }
                        fputcsv($file, array(
                            $value->center_no,
                            $value->center_name,
                            $value->candidate_no,
                            $value->candidate_other_name,
                            $value->candidate_surname,
                            number_format($candidate_total, 2, '.', ''),
                            $value->sponser
                        ));

                        $sponsor_O_total +=  $candidate_total;
                    }
                }
            }
            fputcsv($file, $columns);
            fputcsv($file, $columns);
            $sponsor_M_total = 0;
            if ($sponsor_M) {
                foreach ($sponsor_M  as $value) {
                    $candidate_total =  0;
                    $candidate_total +=  $schoolFees->local_fee + $schoolFees->registration_fee;
                    $subjects = explode(",", $value->subjects);
                    foreach ($subjects as $subject) {
                        $subject_code = explode(" ", $subject);
                        if (in_array($subject_code[0], $practicalSubjects)) {
                            $candidate_total += ($schoolFees->subject_fee) + $schoolFees->practical_subject_fee;
                        } else {
                            $candidate_total += ($schoolFees->subject_fee);
                        }
                    }
                    fputcsv($file, array(
                        $value->center_no,
                        $value->center_name,
                        $value->candidate_no,
                        $value->candidate_other_name,
                        $value->candidate_surname,
                        number_format($candidate_total, 2, '.', ''),
                        $value->sponser
                    ));

                    $sponsor_M_total +=  $candidate_total;
                }
            }
            fputcsv($file, $columns);
            fputcsv($file, $columns);
            $sponsor_N_total = 0;
            if ($sponsor_N) {
                foreach ($sponsor_N as $value) {
                    $candidate_total =  0;
                    $candidate_total +=  $schoolFees->local_fee + $schoolFees->registration_fee;
                    $subjects = explode(",", $value->subjects);
                    foreach ($subjects as $subject) {
                        $subject_code = explode(" ", $subject);
                        if (in_array($subject_code[0], $practicalSubjects)) {
                            $candidate_total += ($schoolFees->subject_fee) + $schoolFees->practical_subject_fee;
                        } else {
                            $candidate_total += ($schoolFees->subject_fee);
                        }
                    }
                    fputcsv($file, array(
                        $value->center_no,
                        $value->center_name,
                        $value->candidate_no,
                        $value->candidate_other_name,
                        $value->candidate_surname,
                        number_format($candidate_total, 2, '.', ''),
                        $value->sponser
                    ));

                    $sponsor_N_total  +=  $candidate_total;
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
