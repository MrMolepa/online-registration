<?php

namespace App\Http\Controllers\School;



use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\fpdf\easyTable;
use App\Libraries\fpdf\exFPDF;
use App\Models\Center;
use  App\Libraries\Payment\Payment;
use App\Models\Candidate;
use App\Models\CenterCandidate;
use App\Models\Session;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    //
    public function index()
    {
        $center = auth()->user()->center_no;
        $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        $levels = DB::table('center_candidate')->select(
            [
                'center_candidate.level'
            ],
        )
            ->where('center_no', '=', $center)
            ->where('financial_year', '=',  $financial_year)
            ->distinct()
            ->orderBy('level')
            ->get()->pluck('level');
        return view('school.reports', compact('levels'));
    }

    public function printTimatable(Request $request)
    {
        ob_start();
        $center = Center::with('subjects')->where('center_no', '=',auth()->user()->center_no)->first();
        $centerSessions = json_decode($center->sessions, true);
        $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        $session = Session::whereIn('session', $centerSessions)->where('financial_year', '=', $financial_year)->first();
        $level  =   $center->level;

        // $password = $center;
        $pdf = new exFPDF();
        // $pdf->SetProtection(array('print'), $password);
        $pdf->SetMargins(8, 34, 8);
        $pdf->AliasNbPages();
        $pdf->SetFont('helvetica', '', 10);
        $candidates = DB::table('candidate_subject')
            ->select(
                'center_candidate.candidate_no',
                'center_candidate.id',
                'center_candidate.type',
                'center_candidate.subject_number',
                'candidates.candidate_surname',
                'candidates.candidate_other_name',
                'candidates.date_of_birth',
                'candidates.gender',
                'center_candidate.sponser',
                DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code,' ',candidate_subject.subject_option)
            order by candidate_subject.subject_code separator ',') as subjects")
            )
            ->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('center_candidate', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
                $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
            })
            ->leftJoin('guardians', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'guardians.candidate');
            })
            ->leftJoin('addresses', function ($join) {
                $join->on('candidate_subject.national_id', '=', 'addresses.user_id');
                $join->where('addresses.user_type', '=', Candidate::class);
            })
            ->groupBy('center_candidate.candidate_no')
            ->where('center_candidate.center_no', '=',  $center->center_no)
            ->where('center_candidate.level', '=',   $level)
            ->where('center_candidate.financial_year', '=', $session->financial_year)
            ->where('center_candidate.session', '=', $session->session)
            ->whereNotNull('guardians.candidate')
            ->whereNotNull('center_candidate.email')
            ->whereNotNull('addresses.user_id')
            ->orderBy('candidates.candidate_no', "ASC")
            ->cursor();
        $table = new easyTable($pdf, '{60,60,80, 60,80}', 'align:L{LLCC};border:0; border-color:#a1a1a1; ');
        $isPrintHeader = false;
        foreach ($candidates as  $result) {
            if (is_paid_sponsored($result->id)->status) {
                $pdf->AddPage();
                $table->rowStyle('valign:M;border:0;paddingY:1;' . "#ccf2ff");
                $table->easyCell("Name of Centre", 'align:C;');
                $table->easyCell($center->center_no . ': ' . $center->center_name . '  (' . $center->district . ')', 'colspan:2; align:C;');
                $table->easyCell(date('d-m-Y '), ' align:C;');
                $table->printRow(4);


                $table->rowStyle('align:{CCCCC};valign:M;bgcolor:#000000; font-color:#ffffff; font-family:times; font-style:B;');
                $table->easyCell('Candidate No. ');
                $table->easyCell('Candidate Names', 'colspan:2; align:C;');
                $table->easyCell('Sex');
                $table->easyCell('DOB');
                $table->printRow();


                $table->rowStyle('valign:M;border:0;paddingY:1;' . "#ccf2ff");
                $table->easyCell($result->candidate_no, 'align:C;');
                $table->easyCell($result->candidate_other_name . ' ' . $result->candidate_surname, 'colspan:2; align:C;');
                $table->easyCell($result->gender);
                $table->easyCell(date('d-m-Y ', strtotime($result->date_of_birth)), ' align:C;');
                $table->printRow(4);


                $subjects = explode(",", $result->subjects);

                foreach ($subjects as $subject) {

                    $code = explode(" ", $subject);


                    // echo $code[0] . " " . $code[2];

                    // $sql_timetable = 'select * from lgcsetimetable where subject_code = "' . $code[0] . '" ';
                    $results_timetable = DB::table('timetable')
                      ->where('subject_code', '=', $code[0])
                      ->where('level', '=', $level)
                      ->where('session', '=', $session->session);

                    // MATHEMATICS core
                    if ($code[1] == "A" &&  $code[0] == "0178") {
                        // $sql_timetable .= ' and paper_no in (01,03)';
                        $results_timetable = $results_timetable->whereIn('paper_no', ['01', '03']);
                        // MATHEMATICS Extended
                    } elseif ($code[1] == "B" &&  $code[0] == "0178") {
                        // $sql_timetable .= ' and paper_no in (02,04)';
                        $results_timetable = $results_timetable->whereIn('paper_no', ['02', '04']);
                        //PHYSICAL SCIENCE core
                    } elseif ($code[1] == "A" &&  $code[0] == "0181") {
                        $results_timetable = $results_timetable->whereIn('paper_no', ['01', '02', '04']);
                        // $sql_timetable .= 'and paper_no in (01,02,04)';

                        // PHYSICAL SCIENCE Extented
                    } elseif ($code[1] == "B" &&  $code[0] == "0181") {
                        // $sql_timetable .= ' and paper_no in (01,03,04)';
                        $results_timetable = $results_timetable->whereIn('paper_no', ['01', '03', '04']);
                    }

                    $results_timetable = $results_timetable->get();

                    foreach ($results_timetable as $result_timetable) {

                        if (!$isPrintHeader) {


                            $table->rowStyle('font-style:B; border:B;border-color:#a1a1a1;');
                            $table->easyCell('Subject');
                            $table->easyCell($result_timetable->subject_code, ' align:L; ');
                            $table->easyCell($result_timetable->subject_name, 'colspan:3; align:L; ');
                            $table->printRow();
                            $isPrintHeader = true;
                        }


                        $table->rowStyle('border:B;border-color:#a1a1a1;');
                        $table->easyCell($result_timetable->paper_no, 'colspan:2; paddingX:20;');
                        $table->easyCell($result_timetable->pape_desc, 'colspan:2 align:L;');
                        $table->easyCell(date('l  F  d Y H:i', strtotime($result_timetable->date_time)) . " - " . date('H:i', strtotime($result_timetable->endTime)), 'colspan:3; align:C;');
                        $table->printRow();
                    }
                    $isPrintHeader = false;
                }
            }

        }
        if ($request->has('download')) {
            $pdf->Output("Timetable" . $center->center_no . ".pdf", "F");
            header("Content-type: application/pdf");
            header('Content-disposition: attectment; filename=Timetable' . $center->center_no . '.pdf');
            readfile("Timetable" . $center->center_no . ".pdf");
            unlink("Timetable" . $center->center_no . '.pdf');
        } else {
            $pdf->Output();
            exit;
        }
        ob_end_flush();
    }
    public function sponsorReport(Request $request)
    {
        ob_start();
        $center_no = auth()->user()->center_no;
        $center = Center::with('subjects')->where('center_no', '=', $center_no)->first();
        $centerSessions = json_decode($center->sessions, true);
        $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        $session = Session::whereIn('session', $centerSessions)->where('financial_year', '=', $financial_year)->first();
        $level  = $request->level;
        $center_sponser = DB::table('center_candidate')->select(
            [
                'center_candidate.sponser'
            ],
        )->where('center_no', '=', $center_no)
            ->where('financial_year', '=', $financial_year)
            ->whereIn('session', $centerSessions)
            ->distinct()
            ->orderBy('sponser')
            ->get()->pluck('sponser')->toArray();

        $sponsers = DB::table('funders')->select(
            '*'
        )
            ->whereIn('sponsor', $center_sponser)
            ->get();


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
            ->where('sessions.session', '=',  $session->session)
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
            ->where('sessions.session', '=',  $session->session)
            ->where('fees_stracture.financial_year', '=', $financial_year)
            ->first();
        $practicalSubjects = ['0179', '0189', '0191', '0192', '0194', '0417', '0190'];
        $delf = Subject::whereHas('selectedDiscipline', function ($q) {
            $q->where('name', '=', 'LGCSE7');
        })->get()->pluck('subject_code')->toArray();
        $exam_session = "November " . date("Y");

        $pdf = new exFPDF();
        $pdf->SetMargins(8, 36, 8);
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);
        $table1 = new easyTable($pdf, 4);
        $table1->rowStyle('font-size:9;');
        $table1->easyCell("<b>Office:</b> ECoL\n<b>Level :</b> $level \n<b>Session:</b> $exam_session\n<b>Centre:</b> $center->centers_no  $center->center_name", 'align:L;colspan:2');
        $table1->easyCell('SPONSOR REPORT', 'align:L;');
        $table1->easyCell('');
        $table1->printRow();
        $table1->endTable(2);
        //====================================================================
        $table = new easyTable($pdf, '{40,40,40,60,40}', 'align:C{LLLLL};border:1; border-color:#a1a1a1; ');

        $count = 0;
        foreach ($sponsers as $key => $sponsor) {

            $sponsor_total = 0;
            $candidates = DB::table('candidate_subject')
                ->select(
                    'center_candidate.candidate_no',
                    'center_candidate.type',
                    'center_candidate.subject_number',
                    'center_candidate.national_id',
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
                    $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
                })
                ->groupBy('center_candidate.candidate_no')
                ->where('center_candidate.center_no', '=', $center_no)
                ->where('center_candidate.level', '=', $level)
                ->where('center_candidate.financial_year', '=', $financial_year)
                ->where('center_candidate.sponser', '=', $sponsor->sponsor)
                ->where('center_candidate.session', '=',   $session->session)
                ->orderBy('candidates.candidate_surname', "ASC")
                ->cursor();

            //====================================================================
            $pdf->AddPage();
            $table->rowStyle('align:{LLLL};valign:M;bgcolor:#000000; font-color:#ffffff; font-family:times; font-style:B;');
            $table->easyCell('Sponsor');
            $table->easyCell('National Id');
            $table->easyCell('Candidate No.');
            $table->easyCell('Candidate Name');
            $table->easyCell('Amount');
            $table->printRow();

            foreach ($candidates as $candidate) {
                $i = 1;
                $bgcolor = '';
                if ($i % 2 == 0) {
                    $bgcolor = 'bgcolor:#F3F5F8;';
                }
                if ($count == 32) {
                    $pdf->AddPage();
                    $table->rowStyle('align:{LLLL};valign:M;bgcolor:#000000; font-color:#ffffff; font-family:times; font-style:B;');
                    $table->easyCell('Sponsor');
                    $table->easyCell('National Id');
                    $table->easyCell('Candidate No.');
                    $table->easyCell('Candidate Name');
                    $table->easyCell('Amount');
                    $table->printRow();
                    $count = 0;
                }
                $count = $count + 1;

                $table->rowStyle('valign:M;border:LR;paddingY:1;' . $bgcolor);
                $table->easyCell($candidate->sponser);
                $table->easyCell(str_pad($candidate->candidate_no, 9, "0", STR_PAD_LEFT));
                $table->easyCell(str_pad($candidate->national_id, 12, "0", STR_PAD_LEFT));
                $table->easyCell($candidate->candidate_other_name . ' ' . $candidate->candidate_surname);
                $candidate_total =  0;

                $subjects = explode(",", $candidate->subjects);

                if (in_array($candidate->type, [2, 3])) {
                    $candidate_total +=  $schoolPrivate->local_fee + $schoolPrivate->registration_fee;
                    foreach ($subjects as $subject) {
                        $subject_code = explode(" ", $subject);
                        if (in_array($subject_code[0], $practicalSubjects)) {
                            $candidate_total += ($schoolPrivate->subject_fee) + $schoolPrivate->practical_subject_fee;
                        } else if (in_array($subject_code[0], $delf)) {
                            $candidate_total += ($schoolFees->delf_fee);
                        } else {
                            $candidate_total += ($schoolPrivate->subject_fee);
                        }
                    }
                } else {
                    $candidate_total +=  $schoolFees->local_fee + $schoolFees->registration_fee;
                    foreach ($subjects as $subject) {
                        $subject_code = explode(" ", $subject);
                        if (in_array($subject_code[0], $practicalSubjects)) {
                            $candidate_total += ($schoolFees->subject_fee) + $schoolFees->practical_subject_fee;
                        } else if (in_array($subject_code[0], $delf)) {
                            $candidate_total += ($schoolFees->delf_fee);
                        } else {
                            $candidate_total += ($schoolFees->subject_fee);
                        }
                    }
                }
                $table->easyCell('LSL ' . number_format($candidate_total, 2, '.', ''));
                $table->printRow();
                $sponsor_total += $candidate_total;
                $i++;
            }

            $table->rowStyle('bgcolor:153,255,153;');
            $table->easyCell(' ', 'border:0;colspan:3; bgcolor:255,255,255;');
            $table->easyCell("Overall Total for $sponsor->sponsor", 'font-style:IB; align:R');
            $table->easyCell("LSL " . number_format($sponsor_total, 2, '.', ''), 'align:L;');
            $table->printRow();
            $count=0;
            //====================================================================


        }
        $table->endTable();
        if ($request->has('download')) {
            $pdf->Output("Sponsor" . $center_no . ".pdf", "F");
            header("Content-type: application/pdf");
            header('Content-disposition: attectment; filename = Sponsor' . $center_no . '.pdf');
            readfile("Sponsor" . $center_no . ".pdf");
            unlink("Sponsor" . $center_no . '.pdf');
        } else {
            $pdf->Output();
            exit;
        }
    }

    public function entryList(Request $request)
    {
        ob_start();
        $center_no = auth()->user()->center_no;
        $center = Center::with('subjects')->where('center_no', '=', $center_no)->first();
        $centerSessions = json_decode($center->sessions, true);
        $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        $session = Session::whereIn('session', $centerSessions)->where('financial_year', '=', $financial_year)->first();
        $level  = $request->level;
        $center_sponser = DB::table('center_candidate')->select(
            [
                'center_candidate.sponser'
            ],
        )->where('center_no', '=', $center_no)
            ->where('financial_year', '=', $financial_year)
            ->whereIn('session', $centerSessions)
            ->distinct()
            ->orderBy('sponser')
            ->get()->pluck('sponser')->toArray();
        $exam_session = "$session->session " . date("Y");
        $pdf = new exFPDF();
        $pdf->SetMargins(8, 34, 9);
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);
        $entrylist = DB::table('candidate_subject')
            ->select(
                'center_candidate.candidate_no',
                'center_candidate.type',
                'center_candidate.subject_number',
                'candidates.candidate_surname',
                'candidates.candidate_other_name',
                'candidates.date_of_birth',
                'candidates.gender',
                'center_candidate.sponser',
                DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code,' ',candidate_subject.subject_option)
            order by candidate_subject.subject_code separator ',') as subjects")
            )
            ->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('center_candidate', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
                $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
            })
            ->groupBy('center_candidate.candidate_no')
            ->where('center_candidate.center_no', '=',   $center_no)
            ->where('center_candidate.financial_year', '=', $financial_year)
            ->where('center_candidate.level', '=', $request->level)
            ->where('center_candidate.session', '=', $session->session)
            ->whereIn('center_candidate.sponser', $center_sponser)
            ->orderBy('candidates.candidate_surname', "ASC")
            ->get();
        $table = new easyTable($pdf, '{50,80,20,40,20,30,69,20}', 'align:L{LLLLLL};border:0; border-color:#a1a1a1; ');
        $table->rowStyle('align:{LLLLLL};valign:M;bgcolor:#000000; font-color:#ffffff; font-family:times;');
        $table->easyCell('Candidate No. ');
        $table->easyCell('Candidate Names');
        $table->easyCell('Sex');
        $table->easyCell('D.O.B');
        $table->easyCell('Type');
        $table->easyCell('Tot Subs');
        $table->easyCell('Subjects');
        $table->easyCell('SP');
        $table->printRow();

        if ($entrylist) {
            $i = 1;
            $totalCandidates = 0;
            $totalSubjects = 0;
            $count = 0;
            foreach ($entrylist as $value) {
                $bgcolor = '';
                if ($i % 2 == 0) {
                    $bgcolor = 'bgcolor:#F3F5F8;';
                }
                $totalCandidates += 1;
                $totalSubjects += $value->subject_number;
                $table->rowStyle('valign:M;border:0;paddingY:2.5;' . $bgcolor);
                $table->easyCell($value->candidate_no);
                $table->easyCell($value->candidate_other_name . ' ' . $value->candidate_surname);
                $table->easyCell($value->gender);
                $table->easyCell($value->date_of_birth);
                $table->easyCell($value->type);
                $table->easyCell($value->subject_number);
                $table->easyCell($value->subjects, "colspan:1");
                $table->easyCell($value->sponser);
                $table->printRow();
                $i++;
                $count++;
                if ($count == 10) {
                    $pdf->SetMargins(8, 36, 8);
                    $pdf->AddPage();
                    $table->rowStyle('align:{LLLLLL};valign:M;bgcolor:#000000; font-color:#ffffff; font-family:times;');
                    $table->easyCell('Candidate No. ');
                    $table->easyCell('Candidate Names');
                    $table->easyCell('Sex');
                    $table->easyCell('D.O.B');
                    $table->easyCell('Type');
                    $table->easyCell('Tot Subs');
                    $table->easyCell('Subjects');
                    $table->easyCell('SP');
                    $table->printRow();
                    $count = 0;
                }
            }
            $table->endTable(4);
            $pdf->AddPage();
            $table1 = new easyTable($pdf, '%{30,30, 20, 20}', 'width:70; border:0; font-size:8; line-height:1.2; paddingX:0');
            $table1->rowStyle('min-height:1.8;paddingY:0.02;');
            $table1->easyCell('', 'colspan:4; bgcolor:#000;');
            $table1->printRow();


            $table1->rowStyle('border:B;border-color:#a1a1a1;');
            $table1->easyCell('Total Candidates ', 'colspan:2;');
            $table1->easyCell($totalCandidates);
            $table1->printRow();
            $table1->rowStyle('border:B;border-color:#a1a1a1;');
            $table1->easyCell('Total Subjects', 'colspan:2;');
            $table1->easyCell($totalSubjects);
            $table1->printRow();
            $table1->endTable(4);
        }
        if (isset($_GET["download"])) {
            $pdf->Output("Entrylist" . $center_no . ".pdf", "F");
            header("Content-type: application/pdf");
            header('Content-disposition: attectment;filename=Entrylist' . $center_no . '.pdf');
            readfile("Entrylist" . $center_no . ".pdf");
            unlink("Entrylist" . $center_no . '.pdf');
        } else {
            $pdf->Output();
            exit;
        }
        ob_end_flush();
    }

    public function entryListPrivate(Request $request)
    {
        ob_start();
        $center = auth()->user()->center_no;
        $financial_years =  CenterCandidate::select(DB::raw('financial_year as year'))
            ->orderBy('year', 'DESC')
            ->where('center_no', '=', $center)
            ->distinct()
            ->get()->pluck('year');

        $year =   $financial_years[0];
        $exam_session = "November " . date("Y");
        $resultCentre = Center::where('center_no', '=',  $center)->first();
        $pdf = new exFPDF();
        $pdf->SetMargins(8, 34, 8);
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 9);

        $table1 = new easyTable($pdf, 3);
        $table1->rowStyle('font-size:12;');
        $table1->easyCell('ENTRYLIST', 'align:L;');
        $table1->easyCell("<b>Office:</b> ECoL\n<b>Level :</b> LGCSE \n<b>Session:</b> $exam_session\n<b>Centre:</b> $resultCentre->centers_no  $resultCentre->center_name", 'align:L;');
        $table1->easyCell('');
        $table1->printRow();
        $table1->endTable(2);



        $entrylist = DB::table('candidate_subject')
            ->select(
                'center_candidate.candidate_no',
                'center_candidate.type',
                'center_candidate.subject_number',
                'candidates.candidate_surname',
                'candidates.candidate_other_name',
                'candidates.date_of_birth',
                'candidates.gender',
                'center_candidate.sponser',
                DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code,' ',candidate_subject.subject_option)
            order by candidate_subject.subject_code separator ',') as subjects")
            )
            ->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('center_candidate', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
                $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
            })
            ->groupBy('center_candidate.candidate_no')
            ->where('center_candidate.center_no', '=', $center)
            ->where('center_candidate.financial_year', '=', $year)
            ->where('center_candidate.level', '=', $request->level)
            ->whereIn('center_candidate.sponser', ['M', 'O', 'N'])
            ->orderBy('candidates.candidate_surname', "ASC")
            ->get();


        $table = new easyTable($pdf, '{50,80,20,40,20,30,69,20}', 'align:L{LLLLLL};border:0; border-color:#a1a1a1; ');

        $table->rowStyle('align:{LLLLLL};valign:M;bgcolor:#000000; font-color:#ffffff; font-family:times;');
        $table->easyCell('Candidate No. ');
        $table->easyCell('Candidate Names');
        $table->easyCell('Sex');
        $table->easyCell('D.O.B');
        $table->easyCell('Type');
        $table->easyCell('Tot Subs');
        $table->easyCell('Subjects');
        $table->easyCell('SP');

        $table->printRow();

        if ($entrylist) {
            $i = 1;
            $totalCandidates = 0;
            $totalSubjects = 0;
            $count = 0;
            foreach ($entrylist as $value) {
                $bgcolor = '';
                if ($i % 2 == 0) {
                    $bgcolor = 'bgcolor:#F3F5F8;';
                }

                $totalCandidates += 1;
                $totalSubjects += $value->subject_number;

                $table->rowStyle('valign:M;border:0;paddingY:2.5;' . $bgcolor);
                $table->easyCell($value->candidate_no);
                $table->easyCell($value->candidate_other_name . ' ' . $value->candidate_surname);
                $table->easyCell($value->gender);
                $table->easyCell($value->date_of_birth);
                $table->easyCell($value->type);
                $table->easyCell($value->subject_number);
                $table->easyCell($value->subjects, "colspan:1");
                $table->easyCell($value->sponser);
                $table->printRow();
                $i++;
                $count++;

                if ($count == 20) {
                    $pdf->SetMargins(8, 36, 8);
                    $pdf->AddPage();
                    $table->rowStyle('align:{LLLLLL};valign:M;bgcolor:#000000; font-color:#ffffff; font-family:times;');
                    $table->easyCell('Candidate No. ');
                    $table->easyCell('Candidate Names');
                    $table->easyCell('Sex');
                    $table->easyCell('D.O.B');
                    $table->easyCell('Type');
                    $table->easyCell('Tot Subs');
                    $table->easyCell('Subjects');
                    $table->easyCell('SP');

                    $table->printRow();
                    $count = 0;
                }
            }
            $table->endTable(4);
            $pdf->AddPage();
            $table1 = new easyTable($pdf, '%{30,30, 20, 20}', 'width:70; border:0; font-size:8; line-height:1.2; paddingX:0');


            $table1->rowStyle('min-height:1.8;paddingY:0.02;');
            $table1->easyCell('', 'colspan:4; bgcolor:#000;');
            $table1->printRow();


            $table1->rowStyle('border:B;border-color:#a1a1a1;');
            $table1->easyCell('Total Candidates ', 'colspan:2;');
            $table1->easyCell($totalCandidates);
            $table1->printRow();
            $table1->rowStyle('border:B;border-color:#a1a1a1;');
            $table1->easyCell('Total Subjects', 'colspan:2;');
            $table1->easyCell($totalSubjects);
            $table1->printRow();

            $table1->endTable(4);
        }
        if (isset($_GET["download"])) {
            $pdf->Output("Entrylist" . $center . ".pdf", "F");
            header("Content-type: application/pdf");
            header('Content-disposition: attectment; filename = Entrylist' . $center . '.pdf');
            readfile("Entrylist" . $center . ".pdf");
            unlink("Entrylist" . $center . '.pdf');
        } else {
            $pdf->Output();
            exit;
        }

        ob_end_flush();
    }
}
