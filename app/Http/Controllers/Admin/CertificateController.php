<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Libraries\fpdfcertificate\easyTable;
use App\Libraries\fpdfcertificate\exFPDF;
use App\Models\Center;

class CertificateController extends Controller
{
    public function index()
    {


        $centers = Center::orderBy('center_no', 'ASC')->where('level', '=', "LGCSE")->get();

        return view('admin.certificates.certificates', compact('centers'));
    }

    public function print(Request $request)
    {

        $certificate = $request->statement_type;
        switch ($certificate) {
            case 'LBSE':
                if (!isset($request->center_no) || !isset($request->candidate_no)) {
                    $pdf = new exFPDF();
                    $pdf->SetTopMargin(78);
                    $fontSize = 9.5;
                    $pdf->SetFont('courier', '',  $fontSize);
                    $centers = Center::orderBy('center_no', 'ASC')->get();
                    $width = $pdf->GetPageWidth() - 10;  // Width of Current Page
                    $height = $pdf->GetPageHeight() - 10; // Height of Current Page
                    $candidateY = 93;
                    $centerY = 115;
                    $gradesY = 143;
                    $candidate_info =  DB::table('lbse_results');
                    if (isset($request->center_no)) {
                        $candidate_info = $candidate_info->where('center_no', '=', $request->center_no);
                    }
                    if (isset($request->candidate_no)) {
                        $candidate_info = $candidate_info->where('candidate_no', '=', $request->candidate_no);
                    }
                    $candidate_info = $candidate_info->groupBy('candidate_no')
                        ->orderBy('candidate_no', 'ASC')
                        ->get();
                    foreach ($candidate_info as $key => $candidate) {
                        $candidate_results =  DB::table('lbse_results')
                            ->where('candidate_no', '=', $candidate->candidate_no)
                            ->orderBy('candidate_no', 'ASC')
                            ->orderBy('subject_code', 'ASC')
                            ->get();

                        //########################################################
                        //LBSE Statemnt of results
                        $pdf->AddPage();
                        $fullName = $candidate->other_name . ' ' . $candidate->surname;
                        $candidate_no = $candidate->candidate_no;
                        $date_of_birth = $candidate->date_of_birth;
                        $center_no = $candidate->center_no;
                        $schoolName = $candidate->center_name;
                        $subject_code = $candidate->subject_code;
                        $subject_name = $candidate->subject_name;

                        //  Candidate Info
                        $pdf->SetXY(10,  $candidateY);
                        $pdf->Cell($width * 2 / 4, 6, $fullName, 0);
                        $pdf->SetXY(87,  $candidateY);
                        $pdf->Cell($width * 1 / 4, 6, " $date_of_birth", 0);
                        $pdf->SetXY(140,  $candidateY);
                        $pdf->Cell($width * 1 / 4, 6, $center_no . "/" .  str_pad($candidate_no, 9, '0', STR_PAD_LEFT), 0);
                        $pdf->Ln();
                        //Center Info
                        $pdf->SetXY(10,   $centerY);
                        $pdf->Cell($width * 3 / 4, 6, "$schoolName", 0);
                        $pdf->SetXY(140,   $centerY);
                        $pdf->Cell($width * 1 / 4, 6, "November 2022", 0);
                        $pdf->Ln();
                        // Subject Code And grade
                        foreach ($candidate_results as $key =>  $candidate_result) {
                            $marks =  $candidate_result->marks;
                            $level = "";
                            if ($marks <= 60 && $marks >= 46) {
                                $level = "ADVANCED";
                            } else if ($marks <= 45 && $marks >= 30) {
                                $level = "PROFICIENT";
                            } else if ($marks <= 29 && $marks >= 22) {
                                $level = "SATISFACTORY";
                            } else if ($marks <= 21 && $marks >= 1) {
                                $level = "UNSATISFACTORY";
                            } else if ($marks == 0) {
                                $level = "ABSENT";
                            }


                            $subject_code = str_pad($candidate_result->subject_code, 4, "0", STR_PAD_LEFT);
                            $subject_name = $candidate_result->subject_name;
                            $space = $key * 3.5;
                            $pdf->SetXY(10, $gradesY + $space);
                            $pdf->Cell(40, 6, "$subject_code", 0);
                            $pdf->SetXY(49,  $gradesY +   $space);
                            $pdf->Cell(90, 6, "$subject_name", 0);
                            $pdf->SetXY(151,  $gradesY +  $space);
                            $pdf->Cell(70, 6, "$level", 0);
                            $pdf->Ln();
                        }
                    }
                    return  $pdf->Output("LBSE Statement Result"  . ".pdf", "I");
                    $pdf->Output("LBSE Statement Result"  . ".pdf", "F");
                    header("Content-type: application/pdf");
                    header('Content-disposition: attectment; filename = LBSE Statement Result'   . '.pdf');
                    readfile("LBSE Statement Result" . ".pdf");
                    unlink("LBSE Statement Result" . '.pdf');
                }
                break;
            case 'LGCSE':
                if (!isset($request->center_no) || !isset($request->candidate_no)) {
                    $pdf = new exFPDF();
                    $pdf->SetTopMargin(78);
                    $fontSize = 9.5;
                    $pdf->SetFont('courier', '',  $fontSize);
                    $width = $pdf->GetPageWidth() - 10;  // Width of Current Page
                    $height = $pdf->GetPageHeight() - 10; // Height of Current Page
                    $candidateY = 92;
                    $centerY = 110;
                    $gradesY = 135;
                    $candidate_info =  DB::table('lgcsepasslist');
                    if (isset($request->center_no)) {
                        $candidate_info = $candidate_info->where('centreNo', '=', $request->center_no);
                    }
                    if (isset($request->candidate_no)) {
                        $candidate_info = $candidate_info->where('candidateNo', '=', $request->candidate_no);
                    }
                    $candidate_info = $candidate_info
                        ->groupBy('candidateNo')
                        ->orderBy('candidateNo', 'ASC')
                        ->get();
                    foreach ($candidate_info as $key => $candidate) {
                        $candidate_results =  DB::table('lgcsepasslist')
                            ->where('candidateNo', '=', $candidate->candidateNo)
                            ->orderBy('candidateNo', 'ASC')
                            ->orderBy('subjectCode', 'ASC')
                            ->get();
                        //########################################################
                        // Statemnt of results
                        $pdf->AddPage();
                        $fullName = $candidate->name . ' ' . $candidate->surname;
                        $candidate_no = $candidate->candidateNo;
                        $date_of_birth = $candidate->dateOfBirth;
                        $center_no = $candidate->centreNo;
                        $schoolName =  $candidate->schoolname;
                        //  Candidate Info
                        $pdf->SetXY(16,  $candidateY);
                        $pdf->Cell($width * 2 / 4, 6, $fullName, 0);
                        $pdf->SetXY(95,  $candidateY);
                        $pdf->Cell($width * 1 / 4, 6, " $date_of_birth", 0);
                        $pdf->SetXY(148,  $candidateY);
                        $pdf->Cell($width * 1 / 4, 6, $center_no . "/" .  str_pad($candidate_no, 9, '0', STR_PAD_LEFT), 0);
                        $pdf->Ln();
                        //Center Info
                        $pdf->SetXY(16,   $centerY);
                        $pdf->Cell($width * 3 / 4, 6, "$schoolName", 0);
                        $pdf->SetXY(145,   $centerY);
                        $pdf->Cell($width * 1 / 4, 6, "June 2023", 0);
                        $pdf->Ln();
                        foreach ($candidate_results as $key =>  $candidate_result) {
                            // centreNo`, `schoolname`, `personSerial`, `candidateNo`, `dateOfBirth`, `name`, `surname`, `grade`, `subjectCode`, `subjectName`
                            $marks = $candidate_result->grade;
                            $subject_code = str_pad($candidate_result->subjectCode, 4, "0", STR_PAD_LEFT);
                            $subject_name = $candidate_result->subjectName;
                            $space = $key * 3.5;
                            $pdf->SetXY(16, $gradesY + $space);
                            $pdf->Cell(40, 6, "$subject_code", 0);
                            $pdf->SetXY(66,  $gradesY +   $space);
                            $pdf->Cell(90, 6, "$subject_name", 0);
                            $pdf->SetXY(162,  $gradesY +  $space);
                            $pdf->Cell(70, 6, "$marks", 0);
                            $pdf->Ln();
                        }
                    }
                    return  $pdf->Output("LGCSE Statement Result"  . ".pdf", "I");
                    $pdf->Output("LGCSE Statement Result"  . ".pdf", "F");
                    header("Content-type: application/pdf");
                    header('Content-disposition: attectment; filename = LGCSE Statement Result'   . '.pdf');
                    readfile("LGCSE Statement Result" . ".pdf");
                    unlink("LGCSE Statement Result" . '.pdf');
                }
                break;
            default:
                break;
        }
    }
}
