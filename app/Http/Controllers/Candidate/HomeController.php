<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Libraries\fpdf\easyTable;
use App\Libraries\fpdf\exFPDF;
use App\Models\Address;
use App\Models\Candidate;
use App\Models\CandidateArrangement;
use App\Models\Center;
use App\Models\CenterCandidate;
use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\Subject;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;

class HomeController extends Controller
{


    public function index()
    {


        $national_id = auth()->user()->national_id;
        $subjects = DB::table('candidate_subject')
            ->select(
                [
                    'center_candidate.id',
                    'center_candidate.center_no',
                    'centers.center_name',
                    'center_candidate.candidate_no',
                    'center_candidate.national_id',
                    'center_candidate.session',
                    'center_candidate.financial_year',
                    'center_candidate.level',
                    'center_candidate.type',
                    'center_candidate.subject_number',
                    'candidates.candidate_surname',
                    'candidates.candidate_other_name',
                    'candidates.date_of_birth',
                    'candidates.gender',
                    'center_candidate.sponser',
                    'subjects.subject_code',
                    'subjects.subject_name',
                    'candidate_subject.subject_option'
                ],
            )->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('subjects', 'subjects.subject_code', '=', 'candidate_subject.subject_code')
            ->join('center_candidate', function ($join) {
                $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                $join->on('candidate_subject.level', '=', 'center_candidate.level');
                $join->on('candidate_subject.session', '=', 'center_candidate.session');
                $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
            })
            ->join('centers', 'centers.center_no', '=', 'center_candidate.center_no')
            ->groupBy('center_candidate.candidate_no', 'center_candidate.level', 'center_candidate.session', 'subjects.subject_code')
            ->where("center_candidate.national_id", '=', $national_id)
            ->where("center_candidate.financial_year", '=', date('Y') . '-' . (date('Y') + 1))
            ->where("center_candidate.session", '=', auth()->user()->session)
            ->get();


        $candidate =  $subjects->first();
        $amount_paid = Invoice::where([
            ['client_id', $candidate->candidate_no],
            ['financial_year', $candidate->financial_year],
            ['level', $candidate->level],
            ['national_id', $national_id],
            ['session', $candidate->session]
        ])->sum('amount');
        $schoolFees =    DB::table('fees_stracture')
            ->where('candidate_type', '=', 'lgcse-school')
            ->where('financial_year', '=', date('Y') . '-' . (date('Y') + 1))
            ->first();
        $schoolPrivate = DB::table('fees_stracture')
            ->where('candidate_type', '=', 'lgcse-private')
            ->where('financial_year', '=',  date('Y') . '-' . (date('Y') + 1))
            ->first();


        $practicalSubjects = ['0179', '0189', '0191', '0192', '0194', '0417', '0190'];
        $delf = Subject::where('is_delf', '=', 1)->get()->pluck('subject_code')->toArray();
        $total_amount = 0;
        if (in_array($candidate->type, [2, 3])) {
            foreach ($subjects as $subject) {
                if (in_array($subject->subject_code, $practicalSubjects)) {
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
                if (in_array($subject->subject_code, $practicalSubjects)) {
                    $total_amount += ($schoolFees->subject_fee) + $schoolFees->practical_subject_fee;
                } else if (in_array($subject->subject_code, $delf)) {
                    $total_amount += ($schoolFees->delf_fee);
                } else if (in_array($subject, $delf)) {
                    $total_amount += ($schoolPrivate->delf_fee);
                } else {
                    $total_amount += ($schoolFees->subject_fee);
                }
            }
            $total_amount  +=  $schoolFees->local_fee + $schoolFees->registration_fee;
        }
        $total_amount  =  $total_amount - $amount_paid;

        $is_paid = DB::table('late_fees')
            ->select('*')
            ->where('financial_year', '=', $candidate->financial_year)
            ->where('session', '=', $candidate->session)
            ->where('amount', '=',  abs($total_amount))
            ->first();
        if ($is_paid != null) {
            $total_amount += $is_paid->amount;
        }

        $late_fee = DB::table('late_fees')
            ->select('*')
            ->whereDate('start_date', '<=', date("Y-m-d"))
            ->whereDate('end_date', '>=', date("Y-m-d"))
            ->first();
        if ($total_amount > $schoolFees->subject_fee && $late_fee !== null) {
            $total_amount += $late_fee->amount;
        }

        // Approval for Sponsored
        $request_action = DB::table('request_action')
        ->select([DB::raw("count(request_action.id) as total")])
        ->join('requests', 'requests.id', '=', 'request_action.request_id')
        ->join('center_candidate', 'center_candidate.id', '=', 'requests.request_data_id')
        ->join('transitions', 'transitions.id', '=', 'request_action.transition_id')
        ->join('actions', 'actions.id', '=', 'request_action.action_id')
        ->join('processes', 'processes.id', '=', 'actions.process')
        ->join('action_types', 'action_types.id', '=', 'actions.action_type')
        ->where('requests.request_data_id', '=', $candidate->id)
        ->where('requests.request_data', '=', CenterCandidate::class)
        ->where('request_action.is_complete', '=', 1)
        ->where('action_types.name', '=', 'Approve')
        ->groupBy('request_action.request_id')
        ->having(DB::raw("count(request_action.request_id)"),'=',1)
        ->first();
        $timetable = "";
        $is_filled = true;
        $candidate_profile = route('candidate.profile.index');
        $next_kin = route('candidate.profile.kin');
        $center_candidate = DB::table('center_candidate')
            ->select(
                'center_candidate.candidate_no',
            )
            ->join('addresses', function ($join) {
                $join->on('addresses.user_id', '=', 'center_candidate.national_id')
                    ->where('addresses.user_type', '=',  Candidate::class);
            })->join('candidate_arrangement', function ($join) {
                $join->on('candidate_arrangement.candidate_no', '=', 'center_candidate.candidate_no');
            })->whereNotNull('center_candidate.email')
            ->whereNotNull('center_candidate.phone_number')
            ->whereNotNull('addresses.postal_address')
            ->whereNotNull('addresses.physical_address')
            ->whereNotNull('addresses.district')
            ->whereNotNull('addresses.village')
            ->where('center_candidate.candidate_no', '=', $candidate->candidate_no)
            ->where('center_candidate.national_id', '=',  $candidate->national_id)
            ->where('center_candidate.session', '=',  $candidate->session)
            ->where('center_candidate.financial_year', '=',  $candidate->financial_year)
            ->first();
            $gurdian = DB::table('guardians')
            ->select(
                'guardians.candidate',
            )
            ->join('addresses', function ($join) {
                $join->on('addresses.user_id', '=', 'guardians.national_id')
                    ->where('addresses.user_type', '=',   Guardian::class);
            })
            ->whereNotNull('addresses.postal_address')
            ->whereNotNull('addresses.physical_address')
            ->whereNotNull('addresses.district')
            ->whereNotNull('addresses.village')
            ->whereNotNull('guardians.guardian_type')
            ->whereNotNull('guardians.name')
            ->whereNotNull('guardians.surname')
            ->whereNotNull('guardians.phone_number')
            ->whereNotNull('guardians.email')
            ->where('guardians.candidate', '=', $candidate->candidate_no)
            ->first();
            if (!$center_candidate) {
                $is_filled =false;
                $timetable .= "<div class='alert alert-danger' role='alert'>
                                Please  Complete <a href='{$candidate_profile}' class='alert-link'>Candidate Profile</a>.
                            </div>";
            }

            if (!$gurdian) {
                $is_filled =false;
                $timetable .= "<div class='alert alert-danger' role='alert'>
                                Please  Complete <a href='{$next_kin}' class='alert-link'>Next of Kin Profile</a>.
                            </div>";
            }

        if ($total_amount <= 0 || $request_action && $is_filled ) {
            $candidate_no = $candidate->candidate_no;
            $center_no = $candidate->center_no;
            $session = $candidate->session;
            $level = $candidate->level;
            $download = route('candidate.candidate.timetable', [
                'centre_no' => $center_no,
                'candidate_no' => $candidate_no,
                'session' => $session,
                'level' => $level,
                'download' => 1
            ]);
            $send = route('candidate.candidate.timetable', [
                'centre_no' => $center_no,
                'candidate_no' => $candidate_no,
                'session' => $session,
                'level' => $level,
                'download' => 1,
                'send' => 1
            ]);
            $timetable ="<div class='timetable-btns my-2'>
                                <a href='$download' class='btn btn-sm btn-primary download-timetable'><i class='fa fa-download '></i>
                                    Download</a>
                                <a href='$send' class='btn btn-sm btn-primary send-email'><i class=' far fa-paper-alternativee'></i>
                                    Send to
                                    email</a>
                            </div>";
            $timetable  = is_publised($level, $session) ? $timetable : '';
        }



        return view('candidate.home', compact('subjects', 'total_amount', 'amount_paid', 'timetable'));
    }





    public function print(Request $request)
    {
        if ($request->has("download")) {
            ob_start();
            $center = $request->centre_no;
            $candidateNo = $request->candidate_no;
            $session = $request->session;
            $level = $request->level;
            $candidate = DB::table('candidate_subject')
                ->select(
                    [
                        'center_candidate.candidate_no',
                        'center_candidate.session',
                        'center_candidate.level',
                        'center_candidate.type',
                        'center_candidate.subject_number',
                        'candidates.candidate_surname',
                        'candidates.candidate_other_name',
                        'candidates.date_of_birth',
                        'candidates.gender',
                        'center_candidate.sponser',
                        DB::raw("group_concat(DISTINCT concat(candidate_subject.subject_code,' ',candidate_subject.subject_option)
                  order by candidate_subject.subject_code separator ',') as subjects")
                    ],
                )
                ->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
                ->join('center_candidate', function ($join) {
                    $join->on('candidate_subject.candidate_no', '=', 'center_candidate.candidate_no');
                    $join->on('candidate_subject.level', '=', 'center_candidate.level');
                    $join->on('candidate_subject.session', '=', 'center_candidate.session');
                    $join->on('candidate_subject.financial_year', '=', 'center_candidate.financial_year');
                })
                ->join('invoices', function ($join) {
                    $join->on('center_candidate.candidate_no', '=', 'invoices.client_id');
                    $join->on('center_candidate.level', '=', 'invoices.level');
                    $join->on('center_candidate.session', 'invoices.session');
                    $join->on('center_candidate.financial_year', 'invoices.financial_year');
                })
                ->groupBy('center_candidate.candidate_no', 'center_candidate.level', 'center_candidate.session')
                ->where('center_candidate.financial_year', '=', date('Y') . '-' . (date('Y') + 1))
                ->where('center_candidate.level', '=', $level)
                ->where('center_candidate.session', '=', $session)
                ->where('center_candidate.candidate_no', '=', $candidateNo)->first();
            if (!is_null($candidate)) {
                $resultCentre = Center::where('center_no', '=', $center)->first();
                $file = fopen("/home/ecol/ecol.coltech.co.za/Instructions/Instructions.txt", "r");
                $pdf = new exFPDF();
                $pdf->SetMargins(8, 42, 8);
                $pdf->AliasNbPages();

                $pdf->AddPage();
                $column_width = ($pdf->GetPageWidth() - 30);
                $pdf->SetFont('helvetica', 'B', 14);
                $pdf->MultiCell($column_width, 6, "Lesotho General Certificate of Secondary Education (LGCSE)", 0, "C");
                $pdf->Ln(2);
                $pdf->MultiCell($column_width, 6, "Instructions to candidates:");
                $pdf->SetFont('helvetica', '', 13);
                while (!feof($file)) {
                    $line = fgets($file);
                    $pdf->MultiCellBlt($column_width, 6, chr(149), $line);
                    $pdf->Ln(4);
                }
                fclose($file);
                $pdf->Ln(10);
                $pdf->SetFont('helvetica', '', 11);
                $table = new easyTable($pdf, '{60,60,80, 60,80}', 'align:L{LLCC};border:0; border-color:#a1a1a1; ');

                $isPrintHeader = false;
                $pdf->AddPage();
                // QR code
                $candidate_josn = json_decode(json_encode($candidate), true);

                $decodedImg = grCodeGenerator($candidateNo, $candidate_josn);
                $pic = 'data://text/plain;base64,' .  $decodedImg;
                $pdf->Image($pic, 175, 5, 28, 25, 'png');
                $table->rowStyle('valign:M;border:0;paddingY:1;' . "#ccf2ff");
                $table->easyCell("Name of Centre", 'align:C;');
                $table->easyCell($resultCentre->center_no . ': ' . $resultCentre->center_name . '  (' . $resultCentre->district . ')', 'colspan:2; align:C;');
                $table->easyCell(date('d-m-Y '), ' align:C;');
                $table->printRow(4);
                $table->rowStyle('align:{CCCCC};valign:M;bgcolor:#000000; font-color:#ffffff; font-family:times; font-style:B;');
                $table->easyCell('Candidate No ');
                $table->easyCell('Candidate Names', 'colspan:2; align:C;');
                $table->easyCell('Sex');
                $table->easyCell('DOB');
                $table->printRow();


                $table->rowStyle('valign:M;border:0;paddingY:1;' . "#ccf2ff");
                $table->easyCell($candidate->candidate_no, 'align:C;');
                $table->easyCell($candidate->candidate_other_name . ' ' . $candidate->candidate_surname, 'colspan:2; align:C;');
                $table->easyCell($candidate->gender);
                $table->easyCell(date('d-m-Y ', strtotime($candidate->date_of_birth)), ' align:C;');
                $table->printRow(4);
                $subjects = explode(",", $candidate->subjects);
                foreach ($subjects as $subject) {
                    $code = explode(" ", $subject);
                    $results_timetable =  DB::table('timetable')
                        ->where('level', '=',    $level)
                        ->where('session', '=',   $session)
                        ->where('subject_code', '=',   $code[0]);
                    // MATHEMATICS core
                    if ($code[1] == "A" &&  $code[0] == "0178") {
                        $results_timetable = $results_timetable->whereIn('paper_no', [1, 3]);

                        // MATHEMATICS Extended
                    } elseif ($code[1] == "B" &&  $code[0] == "0178") {
                        $results_timetable = $results_timetable->whereIn('paper_no', [2, 4]);

                        //PHYSICAL SCIENCE core
                    } elseif ($code[1] == "A" &&  $code[0] == "0181") {
                        $results_timetable = $results_timetable->whereIn('paper_no', [1, 2, 4]);


                        // PHYSICAL SCIENCE Extented
                    } elseif ($code[1] == "B" &&  $code[0] == "0181") {
                        $results_timetable = $results_timetable->whereIn('paper_no', [1, 3, 4]);
                    } else {
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

                if ($request->send) {
                    $cadidate = CenterCandidate::where('candidate_no', '=', $candidateNo)->first();
                    $email = $cadidate->email;
                    $pdfdoc = $pdf->Output('S', "Timetable" . $candidateNo  . ".pdf");

                    $mail = new PHPMailer(true);

                    try {
                        //Server settings
                        // $mail->SMTPDebug = 1;                      // Enable verbose debug output
                        $mail->isSMTP();                                            // Send using SMTP
                        $mail->SMTPSecure = 'tls';         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                        $mail->Host = "smtp.gmail.com";
                        // Set the SMTP server to send through
                        $mail->SMTPAuth = true;                                   // Enable SMTP authentication
                        $mail->SMTPKeepAlive = true;
                        //  $mail->Mailer = “smtp”;
                        $mail->Username   = 'noreply@ecol.org.ls';                     // SMTP username
                        $mail->Password   = 'Ec0l.OTP2024@wCiRaPNn7%}^9w5-';                               // SMTP password
                        $mail->Port = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above


                        //Recipients
                        $mail->setFrom('noreply@ecol.org.ls', 'Examinations Council of Lesotho');
                        // sender
                        $mail->addAddress($email);
                        $mail->addStringAttachment($pdfdoc, "Timetable" . $candidateNo  . ".pdf");


                        $body = "Hi Please find the attached file
        		<br><br>

        		<br><br>
        		Thank you
        		<br>
        		Examinations Council of Lesotho
        		<br><br>For further assistance concerning registration, please contact us:<br>
        		+26669531499 / +266 56876016  <br> support@ecol.org.ls or examscouncil@ecol.org.ls";
                        // Content
                        $mail->isHTML(true);                                  // Set email format to HTML
                        $mail->Subject = 'Timetable for ' . $candidateNo;
                        $mail->Body    = $body;
                        $mail->AltBody = strip_tags($body);
                        if ($mail->send()) {
                            return response()->json(['success' => "Please check your email"]);
                        } else {
                            return response()->json(['fail' => "Failed"]);
                        }
                    } catch (Exception $e) {
                        return response()->json(['success' => 1]);
                    }
                }
                ob_end_flush();
                $pdf->Output('D', "Timetable" . $candidateNo  . ".pdf");
                exit;
            } else {
                return  redirect()->back();
            }
        } else {
            return  redirect()->back();
        }
    }
}
