<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Libraries\fpdfcertificate\easyTable;
use App\Libraries\fpdfcertificate\exFPDF;
use App\Models\Center;
use App\Models\CenterCandidate;
use App\Models\OptionHeader;
use App\Models\PdfTemplate;
use App\Services\PDFService;
use Illuminate\Support\Arr;
use setasign\Fpdi\Fpdi;

class OverPrintController extends Controller
{
    public function index(Request $request)
    {




        $years =  CenterCandidate::select(DB::raw('financial_year'))
            ->orderBy('financial_year', 'DESC')
            ->distinct()
            ->get();
        $financial_year = $years->first()->financial_year;

        $districts = DB::table('centers')
            ->join('center_candidate', 'centers.center_no', '=', 'center_candidate.center_no')
            ->select('centers.district', 'centers.district_code')
            ->groupBy('center_candidate.center_no')
            ->where('center_candidate.financial_year', '=', $financial_year)
            ->orderBy('centers.center_no', 'ASC')
            ->get()->pluck('district', 'district_code');

        $sessions =  CenterCandidate::select(DB::raw('session'))
            ->where('center_candidate.financial_year', '=', $financial_year)
            ->orderBy('session', 'DESC')
            ->distinct()
            ->get();
        $levels =  CenterCandidate::select(DB::raw('level as level'))
            ->where('center_candidate.financial_year', '=', $financial_year)
            ->orderBy('level', 'DESC')
            ->distinct()
            ->get()
            ->pluck('level');


        $pdfTemplates = PdfTemplate::get();
        if ($request->ajax()) {
            $center = $request->center_no;
            $district = $request->district;
            $level = $request->level;
            $centers = DB::table('centers')
                ->select('centers.center_no', 'centers.center_name')
                ->join('center_candidate', 'centers.center_no', '=', 'center_candidate.center_no')
                ->groupBy('center_candidate.center_no')
                ->where('center_candidate.financial_year', '=', $financial_year);
            $subjects =  DB::table('over_print_subjects');

            if (!empty($center)) {
                $centers = $centers->where('center_candidate.center_no', '=', $center);
            }
            if (!empty($district)) {
                $centers = $centers->where('centers.district_code', '=', $district);
            }
            if (!empty($level)) {
                $subjects =  $subjects->where('level', '=',  $level);
                $centers = $centers->where('center_candidate.level', '=', $level);
            }

            $subjects = $subjects->orderBy('subject_code', 'ASC')
                ->get();

            $centers = $centers->orderBy('center_no', 'ASC')
                ->get();


            return response()->json(['centers' => $centers, 'subjects' => $subjects]);
        }

        return view('admin.pdf.overprint', compact('districts', 'levels', 'sessions', 'years', 'pdfTemplates'));
    }
    public function print(Request $request)
    {


        $center = $request->center_no;
        $district = $request->district;
        $level = $request->level;
        $subject = $request->subject;

        $financial_year = (date('m') <= 2) ? (date('Y') - 1) . '-' . date('Y') : date('Y') . '-' . (date('Y') + 1);
        $over_print_candidates = DB::table('candidate_subject')
            ->select(
                'center_candidate.center_no as center_no',
                'centers.center_name as center_name',
                'centers.district_code as district_code',
                'center_candidate.candidate_no as candidate_no',
                'center_candidate.national_id as national_id',
                'center_candidate.session as session',
                'center_candidate.financial_year as financial_year',
                'center_candidate.level as level',
                'center_candidate.type as type',
                'candidates.candidate_other_name as candidate_other_name',
                'candidates.candidate_surname as candidate_surname',
                'candidates.date_of_birth as date_of_birth',
                'candidates.gender as gender',
                'candidate_subject.id as id',
                'center_candidate.sponser as sponser',
                'candidate_subject.subject_code as subject_code',
                'candidate_subject.subject_option as subject_option'
            )
            ->join('candidates', 'candidates.candidate_no', '=', 'candidate_subject.candidate_no')
            ->join('center_candidate', function ($join) {
                $join->on('center_candidate.candidate_no', '=', 'candidate_subject.candidate_no')
                    ->on('center_candidate.level', '=', 'candidate_subject.level')
                    ->on('center_candidate.session', '=', 'candidate_subject.session')
                    ->on('center_candidate.financial_year', '=', 'candidate_subject.financial_year');
            })
            ->join('centers', 'centers.center_no', '=', 'center_candidate.center_no')
            ->groupBy('center_candidate.level', 'center_candidate.session', 'center_candidate.candidate_no', 'candidate_subject.subject_code');

        if (!empty($center)) {
            $over_print_candidates = $over_print_candidates->where('center_candidate.center_no', '=', $center);
        }
        if (!empty($district)) {
            $over_print_candidates = $over_print_candidates->where('centers.district_code', '=', $district);
        }
        if (!empty($level)) {
            $over_print_candidates = $over_print_candidates->where('center_candidate.level', '=', $level);
        }

        // $level
        $multpleChoiceSubjects = DB::table('over_print_subjects');



        if (!empty($subject)) {
            $multpleChoiceSubjects  =   $multpleChoiceSubjects->where('subject_code', '=', $subject);
        }
        $multpleChoiceSubjects =  $multpleChoiceSubjects->orderBy('subject_code', 'ASC')
            ->get()->pluck('subject_code')->toArray();


        $pdf = new exFPDF();
        $pdf->SetTopMargin(78);
        $fontSize = 11;
        $pdf->SetFont('Helvetica', '',  $fontSize);
        $width = $pdf->GetPageWidth() - 10;  // Width of Current Page
        $height = $pdf->GetPageHeight() - 10; // Height of Current Page
        // Live
        $file = "/home/ecol/ecol.coltech.co.za/templetes/$level-Template_Answer_Sheet.pdf";
        //$file = public_path() . '/Instructions/Template_Answer_Sheet.pdf';

        $over_print_candidates = $over_print_candidates
            ->where('candidate_subject.financial_year', '=', $financial_year)
            ->whereIn('candidate_subject.subject_code', $multpleChoiceSubjects)
            ->orderBy('centers.center_no', 'asc')
            ->orderBy('candidate_subject.subject_code', 'asc')
            ->orderBy('candidate_subject.subject_option', 'asc')
            ->orderBy('candidates.candidate_no', 'asc')
            ->orderBy('id', "ASC")
            ->each(function (object $candidate) use (&$pdf, $width, $height, &$request, &$file) {
                $invoices = DB::table('invoices')
                    ->where('client_id', '=', $candidate->candidate_no)
                    ->where('national_id', '=', $candidate->national_id)
                    ->where('level', '=', $candidate->level)
                    ->where('session', '=', $candidate->session)
                    ->where('financial_year', '=', $candidate->financial_year)
                    ->first();
                if ($invoices || in_array($candidate->sponser, ['M', 'K'])) {
                    $over_print_subjects =  DB::table('over_print_subjects')
                        ->where('subject_code', '=', $candidate->subject_code)
                        ->orderBy('subject_code', 'ASC')
                        ->first();
                    // Candidate info
                    $candidate_no = str_pad($candidate->candidate_no, 9, '0', STR_PAD_LEFT);
                    $candate_name = $candidate->candidate_other_name;
                    $candate_surname = $candidate->candidate_surname;
                    // Center info
                    $center_no = $candidate->center_no;
                    $center_name = $candidate->center_name;
                    // Subject info
                    $subject_code = $over_print_subjects->subject_code;
                    $subject_name = $over_print_subjects->subject_name;
                    if (in_array($subject_code, ['0181'])) {
                        $optionHeader = OptionHeader::find($candidate->subject_option);
                        $subject_code = $candidate->subject_code . "_" . $optionHeader->alternative_option_code;
                        $subject_name = $over_print_subjects->subject_name . " " . $optionHeader->description;
                    }
                    $exams_date =  date('Y-m-d', strtotime($over_print_subjects->exam_date));


                    //########################################################
                    // Statemnt of results
                    $pdf->AddPage();
                    if (!$request->has('blank')) {
                        $pdf->setSourceFile($file);
                        $tplIdx = $pdf->importPage(1);
                        $size = $pdf->getTemplateSize($tplIdx);
                        $pdf->useTemplate($tplIdx, null, null, $size['width'], $size['height'], true);
                    }
                    $pdf->SetFont('Arial');
                    //  Surname
                    $pdf->SetXY(33.58,  32.5);
                    $pdf->Cell($width * 2 / 4, 6,  $candate_surname, 0);

                    //Other names
                    $pdf->SetXY(39.69,  39.5);
                    $pdf->Cell($width * 2 / 4, 6, $candate_name, 0);

                    // Exams date:
                    $pdf->SetXY(27.12,  46.2);
                    $pdf->Cell($width * 2 / 4, 6, $exams_date, 0);
                    // Subject Name:
                    $pdf->SetXY(80.77,  87.87);
                    $pdf->Cell($width * 2 / 4, 6, $subject_name, 0);
                    // Candidate Number
                    $pdf->Code39(136.14, 18, 'Candidate Number', $candidate_no);
                    // Center Number
                    $pdf->Code39(142, 42, 'Center Number', $center_no);
                    // Subject Code
                    $pdf->Code39(145, 66, 'Subject Code', "$subject_code");


                    $pdf->Rotate(90, 201, 214);
                    $pdf->Text(201, 214,  $center_name);
                    $pdf->Rotate(0);
                }
            });





        $pdf->Output("Scanners"  . ".pdf", "I");
        $pdf->Output("Scanners"  . ".pdf", "F");
        header("Content-type: application/pdf");
        header("Content-disposition: attectment; filename = Scanners" . '.pdf');
        readfile("Scanners" . ".pdf");
        unlink("Scanners" . '.pdf');
        exit;
        ob_end_flush();
    }


    public function overPrint(Request $request)
    {

        $request->validate([
            'template' => 'required',
        ]);

        $template = $request->template;
        $pdfTemplate = PdfTemplate::find($template);
        $inputs = $request->except(['template', 'print', '_token', 'blank']);
        $filteredData = collect($inputs)->reject(function ($value) {
            return is_null($value);
        })->all();

        $values = (object)$filteredData;
        $culumns = $this->getTableColumns($pdfTemplate->data_source);
        $filteredData =  array_map(function ($value) use ($culumns) {
            if (in_array($value, $culumns)) {
                return [
                    'column' => $value,
                    'operator' => "equals"
                ];
            }
        }, array_keys($filteredData));
        $felters = array_filter($filteredData, function ($value) {
            return $value !== null;
        });
        $pdf = new  PDFService();
        $pdf->generatePdf($template, $felters, $values, 100000);
        exit();
    }


    protected function getTableColumns($tableName)
    {
        try {
            $columns = DB::getSchemaBuilder()->getColumnListing($tableName);
            return $columns;
        } catch (\Exception $e) {
            return [];
        }
    }
  }
