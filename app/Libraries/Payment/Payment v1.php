<?php

namespace App\Libraries\Payment;

use App\Models\BankStatement;
use App\Models\CenterOtherCharge;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class Payment
{
    public static function schoolfees($center = null, $level = null, $session = "Novemver", $financial_year = null)
    {

        set_time_limit(0);
        // Fees Setup
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
            ->where('fees_stracture.financial_year', '=', $financial_year)
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
            ->where('fees_stracture.financial_year', '=', $financial_year)
            ->first();
        $practicalSubjects = ['0179', '0189', '0191', '0192', '0194', '0417', '0190'];
        $delf = Subject::whereHas('selectedDiscipline', function ($q) {
            $q->where('name', '=', 'LGCSE7');
        })->get()->pluck('subject_code')->toArray();
        // Sponsor
           $sponsors = DB::table('center_candidate')->select(
            [
                'center_candidate.sponser'
            ],
        )->distinct()
            ->orderBy('sponser')
            ->where('center_candidate.financial_year', '=', $financial_year)
            ->where('center_candidate.session', '=', $session)
            ->where('center_candidate.level', '=', $level)
            ->get();

        $allSponsers = array();
        $totalPaidCenter = 0;
        $total_candidates = 0;
        $total_overdue = 0;
        foreach ($sponsors as  $sponsor) {
            $total_candidate_sponsor = DB::table('center_candidate')
                ->where('center_no', '=', $center)
                ->where('level', '=', $level)
                ->where('session', '=', $session)
                ->where('financial_year', '=', $financial_year)
                ->where('sponser', '=',  $sponsor->sponser)
                ->count();


            $grand_total = 0;
            $candidate_amount_paid = 0;
            $sponsor_overdue = 0;
            $total_subjects = 0;
            $total_practical_subjects = 0;
            $total_delf_subjects = 0;
            DB::table('candidates')
                ->select(
                    'center_candidate.center_no',
                    'center_candidate.candidate_no',
                    'center_candidate.national_id',
                    'center_candidate.id',
                    'center_candidate.type',
                    DB::raw("COALESCE( (SELECT SUM(invoices.amount) FROM invoices  WHERE
                    invoices.client_id =  CONVERT( center_candidate.candidate_no USING UTF8MB4) COLLATE utf8mb4_unicode_ci and
                    invoices.session = center_candidate.session and
                    invoices.financial_year = center_candidate.financial_year
                    ),0) AS  amount"),
                    'center_candidate.subject_number',
                    'center_candidate.sponser',
                    'candidate_subject.financial_year',
                    DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code)
               order by candidate_subject.subject_code separator ',') as subjects"),
                )
                ->join('center_candidate', 'center_candidate.candidate_no', '=', 'candidates.candidate_no')
                ->join('candidate_subject', function ($join) {
                    $join->on('center_candidate.national_id', '=', 'candidate_subject.national_id');
                    $join->on('center_candidate.candidate_no', '=', 'candidate_subject.candidate_no');
                    $join->on('center_candidate.level', '=', 'candidate_subject.level');
                    $join->on('center_candidate.session', '=', 'candidate_subject.session');
                    $join->on('center_candidate.financial_year', '=', 'candidate_subject.financial_year');
                })
                ->groupBy(['center_candidate.candidate_no','center_candidate.financial_year', 'center_candidate.level', 'center_candidate.session'])
                ->where('center_candidate.center_no', '=', $center)
                ->where('center_candidate.level', '=', $level)
                ->where('center_candidate.financial_year', '=',  $financial_year)
                ->where('center_candidate.session', '=',  $session)
                ->where('center_candidate.sponser', '=',  $sponsor->sponser)
                ->orderBy('center_candidate.id', "ASC")
                ->each(function (object $candidate) use (
                    &$total_practical_subjects,
                    &$total_subjects,
                    &$total_delf_subjects,
                    $delf,
                    $practicalSubjects,
                    $schoolPrivate,
                    $schoolFees,
                    &$grand_total,
                    &$sponsor_overdue,
                    &$candidate_amount_paid,
                ) {
                    $subjects = explode(",", $candidate->subjects);
                    $total_amount = 0;
                    if (in_array($candidate->type, [2, 3])) {
                        foreach ($subjects as $subject) {
                            if (in_array($subject, $practicalSubjects)) {
                                $total_practical_subjects += 1;
                                $total_amount += $schoolPrivate->subject_fee + $schoolPrivate->practical_subject_fee;
                            } else if (in_array($subject, $delf)) {
                                $total_delf_subjects += 1;
                                $total_amount += ($schoolPrivate->delf_fee);
                            } else {
                                $total_subjects += 1;
                                $total_amount += $schoolPrivate->subject_fee;
                            }
                        }
                        $total_amount  +=  $schoolPrivate->local_fee + $schoolPrivate->registration_fee;
                        $grand_total += $total_amount;
                    } else {
                        foreach ($subjects as $subject) {
                            if (in_array($subject, $practicalSubjects)) {
                                $total_practical_subjects += 1;
                                $total_amount += $schoolFees->subject_fee + $schoolFees->practical_subject_fee;
                            } else if (in_array($subject, $delf)) {
                                $total_delf_subjects += 1;
                                $total_amount += ($schoolFees->delf_fee);
                            } else {
                                $total_subjects += 1;
                                $total_amount += $schoolFees->subject_fee;
                            }
                        }


                        $total_amount  +=  $schoolFees->local_fee + $schoolFees->registration_fee;
                        $grand_total += $total_amount;
                    }

                    $candidate_amount_paid += $candidate->amount;
                    $sponsor_overdue += $total_amount;
                });



            // foreach ($candidates as $candidate) {
            //     $subjects = explode(",", $candidate->subjects);
            //     $total_amount = 0;
            //     if (in_array($candidate->type, [2, 3])) {
            //         foreach ($subjects as $subject) {
            //             if (in_array($subject, $practicalSubjects)) {
            //                 $total_practical_subjects += 1;
            //                 $total_amount += $schoolPrivate->subject_fee + $schoolPrivate->practical_subject_fee;
            //             } else if (in_array($subject, $delf)) {
            //                 $total_delf_subjects += 1;
            //                 $total_amount += ($schoolPrivate->delf_fee);
            //             } else {
            //                 $total_subjects += 1;
            //                 $total_amount += $schoolPrivate->subject_fee;
            //             }
            //         }
            //         $total_amount  +=  $schoolPrivate->local_fee + $schoolPrivate->registration_fee;
            //         $grand_total += $total_amount;
            //     } else {
            //         foreach ($subjects as $subject) {

            //             if (in_array($subject, $practicalSubjects)) {
            //                 $total_practical_subjects += 1;
            //                 $total_amount += $schoolFees->subject_fee + $schoolFees->practical_subject_fee;
            //             } else if (in_array($subject, $delf)) {
            //                 $total_delf_subjects += 1;
            //                 $total_amount += ($schoolFees->delf_fee);
            //             } else {
            //                 $total_subjects += 1;
            //                 $total_amount += $schoolFees->subject_fee;
            //             }
            //         }


            //         $total_amount  +=  $schoolFees->local_fee + $schoolFees->registration_fee;
            //         $grand_total += $total_amount;
            //     }

            //     $candidate_amount_paid += $candidate->amount;
            //     $sponsor_overdue += $total_amount;
            // }
            $total_overdue += $sponsor_overdue;
            $total_candidates += $total_candidate_sponsor;
            $totalPaidCenter += $candidate_amount_paid;
            $allSponsers[$sponsor->sponser] = array(
                'total_candidate' => $total_candidate_sponsor,
                'sponsor_overdue' => $sponsor_overdue,
                'total_amount_paid' => $candidate_amount_paid,
                'total_subjects' => $total_subjects,
                'practical' => $total_practical_subjects,
                'delf' => $total_delf_subjects
            );
        }
        // Amount paid
        $amount_paid_bank_statement = BankStatement::where('center_id', '=', $center)
            ->where('checked_status', '=', 2)
            ->where('financial_year', '=', $financial_year)
            ->sum('amount_paid');
        // EFT
        $amount_paid_eft = DB::table('invoices')
            ->where('client_id', '=', $center)
            ->where('financial_year', '=', $financial_year)
            ->sum('amount');
        $totalPaidCenter    += ($amount_paid_bank_statement +  $amount_paid_eft);
        // Other Charges
        $otherCharge = CenterOtherCharge::where('center_id', '=', $center)
            ->where('financial_year', '=', $financial_year)
            ->sum('charge');
        $balance = ($total_overdue + $otherCharge)  -  $totalPaidCenter;
        return collect(
            (object) [
                'sponsors' =>  $allSponsers,
                'total_paid' => $totalPaidCenter,
                'other_charges' => $otherCharge,
                'total_candidates' => $total_candidates,
                'total_overdue' => $total_overdue,
                'balance' => $balance
            ],
        );
    }

    public static function privatefees($center = null, $level = null, $session = null, $financial_year = null)
    {
        set_time_limit(0);
        $fees  = DB::table('fees_stracture')
            ->where('candidate_type', '=', "lgcse-private")
            ->where('financial_year', '=', $financial_year)
            ->first();
        $totalCandidates = DB::table('total_candidates')
            ->selectRaw('sum(total_candidates.total) as total')
            ->selectRaw("sum(total_candidates.self) as self");
        if (!is_null($center)) {
            $totalCandidates  = $totalCandidates->where('center_no', '=', $center);
        }

        if (!is_null($level)) {
            $totalCandidates  = $totalCandidates->where('level', '=', $level);
        }
        if (!is_null($session)) {
            $totalCandidates  = $totalCandidates->where('session', '=', $session);
        }
        if (!is_null($financial_year)) {
            $totalCandidates  = $totalCandidates->where('financial_year', '=', $financial_year);
        }

        // Total Candidates
        $totalCandidates = $totalCandidates->first();
        $candidates = array();
        array_push($candidates, $totalCandidates->self);
        $total_self = 0;

        // Total Subjects
        $total_subjects = DB::table('total_subjects')
            ->selectRaw('sum(total_subjects.total) as total')
            ->selectRaw("sum(total_subjects.self) as self");
        if (!is_null($center)) {
            $total_subjects   = $total_subjects->where('center_no', '=', $center);
        }

        if (!is_null($level)) {
            $total_subjects  = $total_subjects->where('level', '=', $level);
        }
        if (!is_null($session)) {
            $total_subjects  = $total_subjects->where('session', '=', $session);
        }

        $total_subjects  = $total_subjects->where('financial_year', '=', $financial_year);
        $total_subjects  =    $total_subjects->first();

        if (is_null($total_subjects)) {
            $total_subjects  = new Collection();
            $total_subjects->self = 0;
        }

        $total_subjects_array = array();

        $total_subjects_array['total_subjects'] = $total_subjects;


        // Calculating Candidates total amount per sponsor
        $total_self = ($totalCandidates->self * ($fees->registration_fee + $fees->local_fee + $fees->bank_charge));

        if ($totalCandidates->self > 0) {
            $total_self +=  ($total_subjects->self * $fees->subject_fee);
        }


        $sponsor = array();
        array_push($sponsor, $total_self);

        $total_students = $candidates[0];
        $total_amount = $total_self;

        $array = array();
        $array['total_amount'] = $total_amount;
        $array['student_number'] = $total_students;
        $array['sponsor'] = $sponsor;
        $array['total_subjects'] = $total_subjects_array;
        $array['candidates'] =     $candidates;
        $array['bank_charge'] = $fees->bank_charge;

        return $array;
    }

    public static function exportschoolfees()
    {
        set_time_limit(0);
        $fileName = 'Reports ' . time() . '.csv';
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
        $centers = DB::table('center_candidate')
            ->select(
                'centers.center_name',
                'center_candidate.center_no',
            )
            ->join('centers', 'center_candidate.center_no', '=', 'centers.center_no')
            ->whereIn('center_candidate.sponser', ['O', 'N', 'M'])
            ->orderBy('center_candidate.center_no', 'ASC')
            ->distinct()
            ->groupBy('center_candidate.center_no')
            ->get();
        $centersFees = collect();
        $financial_year = (date('m') <= 4) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);

        foreach ($centers  as  $center) {
            $schoolResult = Payment::schoolfees($center->center_no, null, null, $financial_year);
            $schoolResult['center_no'] = $center->center_no;
            $schoolResult['center_name'] = $center->center_name;
            $centersFees->push($schoolResult);
        }
        $columns = array('Centre Number', 'Centre Name', '#.Candidates', 'NMDS', 'MoET', 'Other', 'Bank charge', 'Other Charges', 'Total Overdue', 'Paid amount', 'Balance Due', 'Total');
        $callback = function () use ($centersFees, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($centersFees  as $centersFee) {

                $totalOverDue = $centersFee['bank_charge'] + $centersFee['total_charge'] + $centersFee['sponsor'][2];
                $totalPaid = $centersFee['total_paid'];
                $balance = $totalOverDue - $totalPaid;
                $totalAmount = $centersFee['total_amount'] + $centersFee['total_charge'];

                fputcsv($file, array(
                    $centersFee['center_no'],
                    $centersFee['center_name'],
                    $centersFee['student_number'],
                    number_format($centersFee['sponsor'][0], 2, '.', ''),
                    number_format($centersFee['sponsor'][1], 2, '.', ''),
                    number_format($centersFee['sponsor'][2], 2, '.', ''),
                    number_format($centersFee['bank_charge'], 2, '.', ''),
                    number_format($centersFee['total_charge'], 2, '.', ''),
                    number_format($totalOverDue, 2, '.', ''),
                    number_format($centersFee['total_paid'], 2, '.', ''),
                    number_format($balance, 2, '.', ''),
                    number_format($totalAmount, 2, '.', '')
                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public static function exportsprivatefees()
    {
        set_time_limit(0);
        $fileName = 'Reports ' . time() . '.csv';
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $centers = DB::table('center_candidate')
            ->select(
                'centers.center_name',
                'center_candidate.center_no',
            )
            ->join('centers', 'center_candidate.center_no', '=', 'centers.center_no')
            ->whereNotIn('center_candidate.sponser', ['O', 'N', 'M'])
            ->orderBy('center_candidate.center_no', 'ASC')
            ->distinct()
            ->groupBy('center_candidate.center_no')
            ->get();
        $centersFees = collect();
        $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        foreach ($centers  as  $center) {
            $schoolResult = Payment::privatefees($center->center_no, null, null, $financial_year);
            $schoolResult['center_no'] = $center->center_no;
            $schoolResult['center_name'] = $center->center_name;
            $centersFees->push($schoolResult);
        }





        $columns = array('Centre Number', 'Centre Name', '#.Candidates', 'Total Amount');
        $callback = function () use ($centersFees, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($centersFees  as $centerFee) {
                fputcsv($file, array(
                    $centerFee['center_no'],
                    $centerFee['center_name'],
                    $centerFee['candidates'][0],
                    $centerFee['total_amount']
                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
