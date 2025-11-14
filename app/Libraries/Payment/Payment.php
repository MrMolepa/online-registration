<?php

namespace App\Libraries\Payment;

use App\Models\BankStatement;
use App\Models\CenterOtherCharge;
use App\Models\CenterPayment;
use App\Models\FeeFine;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class Payment
{
    public static function schoolfees($center = null, $level = null, $session = "Novemver", $financial_year = null)
    {

        set_time_limit(0);
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
        $grand_total_fine = 0;
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
            $total_fine = 0;
            DB::table('candidate_subject')
                ->select(
                    'centers.center_no',
                    'centers.center_name',
                    'center_candidate.id',
                    'center_candidate.candidate_no',
                    'center_candidate.national_id',
                    'center_candidate.type',
                    'center_candidate.subject_number',
                    'center_candidate.session',
                    'center_candidate.financial_year',
                    'center_candidate.level',
                    'candidates.candidate_surname',
                    'candidates.candidate_other_name',
                    'candidates.date_of_birth',
                    'candidates.gender',
                    DB::raw("COALESCE( (SELECT SUM(fee_candidate_histories.amount) FROM fee_candidate_histories  WHERE
                fee_candidate_histories.candidate_id = center_candidate.id
                and fee_candidate_histories.status='1'
                ),0) AS amount_paid"),
                    DB::raw("COALESCE( (SELECT SUM(fee_candidate_histories.fine) FROM fee_candidate_histories  WHERE
                fee_candidate_histories.candidate_id = center_candidate.id
                and fee_candidate_histories.status='1'
                ),0) AS fine"),
                    DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code)
                order by candidate_subject.subject_code separator ',') as subjects"),
                    'center_candidate.sponser',
                )
                ->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
                ->join('center_candidate', function ($join) {
                    $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                    $join->on('candidate_subject.level', '=', 'center_candidate.level');
                    $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
                    $join->on('candidate_subject.session', '=', 'center_candidate.session');
                })
                ->join('centers', 'centers.center_no', '=', 'center_candidate.center_no')
                ->groupBy(['center_candidate.candidate_no', 'center_candidate.financial_year', 'center_candidate.level', 'center_candidate.session'])
                ->where('center_candidate.center_no', '=', $center)
                ->where('center_candidate.level', '=', $level)
                ->where('center_candidate.financial_year', '=',  $financial_year)
                ->where('center_candidate.session', '=',  $session)
                ->where('center_candidate.sponser', '=',  $sponsor->sponser)
                ->orderBy('center_candidate.id', "ASC")
                ->each(function (object $candidate) use (
                    &$grand_total,
                    &$sponsor_overdue,
                    &$candidate_amount_paid,
                    &$total_fine,
                ) {
                    $subjects = explode(",", $candidate->subjects);
                    $candidate_type = $candidate->type;
                    if ($candidate->type == 2 || $candidate->type == 3) {
                        $candidate_type = 3;
                    }

                    $candidate_level = substr($candidate->level, 0, 2);
                    $subjects = explode(",", $candidate->subjects);
                    array_push($subjects, "-");
                    $fee_group_detail = DB::table('fee_groups')
                        ->select(
                            DB::raw("SUM(fee_group_details.amount) as total_amount"),
                            'fee_group_details.fee_group_id'
                        )
                        ->join('fee_group_details', 'fee_group_details.fee_group_id', '=', 'fee_groups.id')
                        ->join('fee_types', 'fee_types.id', '=', 'fee_group_details.fee_type_id')
                        ->join('sessions', 'sessions.id', '=', 'fee_groups.session_id')
                        ->join('levels', 'levels.id', '=', 'fee_groups.level_id')
                        ->where('sessions.session', '=', $candidate->session)
                        ->where('sessions.financial_year', '=', $candidate->financial_year)
                        ->where('fee_groups.candidate_type', '=',  $candidate_type)
                        ->where('levels.level', 'like', "$candidate_level%")
                        ->whereIn('fee_group_details.subject_code',  $subjects)
                        ->groupBy('fee_groups.id')
                        ->first();

                    $candidate_amount_paid += $candidate->amount_paid;
                    $groupId = $fee_group_detail->fee_group_id;
                    $fee_fine = FeeFine::where('fee_group_id', '=', $groupId)
                        ->where('start_date', '<=',   date('Y-m-d'))
                        ->where('end_date', '>=',   date('Y-m-d'))
                        ->first();
                    $candidate_fine = 0;
                    if ($fee_fine !== null  &&  $fee_group_detail->total_amount > $candidate->amount_paid) {
                        $candidate_fine += $fee_fine->fine_value;
                    }



                    $sponsor_overdue += $fee_group_detail->total_amount + $candidate->fine + $candidate_fine;
                    $grand_total += $fee_group_detail->total_amount;
                    $total_fine  += $candidate->fine;
                });

            $total_overdue += $sponsor_overdue;
            $total_candidates += $total_candidate_sponsor;
            $totalPaidCenter += $candidate_amount_paid;
            $grand_total_fine += $total_fine;
            $balance  = ($sponsor_overdue) -  $candidate_amount_paid;
            $allSponsers[$sponsor->sponser] = array(
                'total_fine' => $total_fine,
                'total_candidate' => $total_candidate_sponsor,
                'sponsor_overdue' => $sponsor_overdue,
                'total_amount_paid' => $candidate_amount_paid,
                'total_subjects' => $total_subjects,
                'balance' =>  $balance,
                'practical' => $total_practical_subjects,
                'delf' => $total_delf_subjects
            );
        }
        // Amount paid
        $amount_paid_bank_statement = CenterPayment::where('center_no', '=', $center)
            ->where('status', '=', 2)
            ->where('financial_year', '=', $financial_year)
            ->sum('amount');
        // EFT
        $amount_paid_eft = DB::table('invoices')
            ->where('client_id', '=', $center)
            ->where('financial_year', '=', $financial_year)
            ->sum('amount');




        $totalPaidCenter    += ($amount_paid_bank_statement +  $amount_paid_eft);
        // Other Charges
        $otherCharge = CenterOtherCharge::where('center_no', '=', $center)
            ->where('financial_year', '=', $financial_year)
            ->sum('amount');


        $balance = ($total_overdue + $otherCharge)  -  $totalPaidCenter;
        return collect(
            (object) [
                'sponsors' =>  $allSponsers,
                'total_paid' => $totalPaidCenter,
                'other_charges' => $otherCharge,
                'total_candidates' => $total_candidates,
                'total_overdue' => $total_overdue,
                'total_fine' => $grand_total_fine,
                'balance' => $balance
            ],
        );
    }









    public static function highestSubject($center = null, $level = null, $session = "November", $financial_year = null)
    {
        set_time_limit(0);
        $maxValue = DB::selectOne("
            SELECT MAX(subject_number) as max_value
            FROM (
                SELECT
                    cc.center_no,
                    cc.level,
                    cc.session,
                    cc.financial_year,
                    COUNT(0) AS subject_number
                FROM candidate_subject
                JOIN center_candidate cc
                    ON cc.candidate_no = candidate_subject.candidate_no
                AND cc.session = candidate_subject.session
                AND cc.financial_year = candidate_subject.financial_year
                LEFT JOIN (
                    SELECT
                        candidate_id,
                        SUM(amount) as total_paid
                    FROM fee_candidate_histories
                    WHERE status = '1'
                    GROUP BY candidate_id
                ) fch
                    ON fch.candidate_id = cc.id
                WHERE
                    cc.session = :session
                    AND cc.financial_year = :financial_year
                    AND cc.level = :level
                    AND cc.center_no = :center
                    AND (
                        (cc.sponser IN ('O','P') AND COALESCE(fch.total_paid,0) > 0)
                        OR (cc.sponser <> 'O' AND COALESCE(fch.total_paid,0) >= 0)
                    )
                GROUP BY
                    candidate_subject.financial_year,
                    cc.center_no,
                    cc.session,
                    candidate_subject.subject_code
            ) center_invigilation
        ", [
            'session' => $session,
            'financial_year' => $financial_year,
            'level' => $level,
            'center' => $center,
        ])->max_value;

        return $maxValue;
    }
}
