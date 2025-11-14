<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\fpdfcertificate\exFPDF;
use App\Models\Center;
use App\Models\CenterCandidate;
use App\Models\InvigilationRole;
use App\Models\InvigilationType;
use Illuminate\Support\Facades\DB;
use setasign\Fpdi\Fpdi;

class InvigilationReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $centers = Center::orderBy('center_no', 'ASC')->where('level', '=', 'LGCSE')->get();
        $districts = Center::orderBy('center_no', 'ASC')->where('level', '=', 'LGCSE')->groupBy('district_code')->get()->pluck('district', 'district_code');
        $levels = CenterCandidate::select(DB::raw('level as level'))->orderBy('level', 'DESC')->distinct()->get()->pluck('level');
        return view('admin.invigilation.contracts.index', compact('centers', 'districts', 'levels'));

        $filePath = public_path('assets/Filled_Contract_Form.pdf');
        $outputFilePath = public_path('assets/Filled_Contract_Form.pdf');
        $this->fillPDFFile($filePath, $outputFilePath);
        return response()->file($outputFilePath);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function exportMultiPdf(Request $request)
    {
        if ($request->file_type == "pdf") {
        // variable contains requests
        $session = empty($request->session) ? 'November' : $request->session;
        $year = empty($request->year) ? '2024-2025' : $request->year;
        $center = $request->center_no;
        $district = $request->district_id;
        // Set query
        $invigilation_contracts = DB::table('invigilator_profile')::with('invigilation_roles.invigilation_types', 'invigilator_paymentamount', 'districts');

        if (!empty($center)) {
            $invigilation_contracts = $invigilation_contracts->where('center_no', '=', $center);
        }
        if (!empty($session)) {
            $invigilation_contracts = $invigilation_contracts->where('session', '=', $session);
        }
        if (!empty($year)) {
            $invigilation_contracts = $invigilation_contracts->where('financial_year', '=', $year);
        }
        if (!empty($district)) {
            $invigilation_contracts = $invigilation_contracts->where('district_id', '=', $district);
        }
        $invigilation_contracts = $invigilation_contracts->get();
        $roles = InvigilationRole::get();
        $types = InvigilationType::get();
        $fpdi = new exFPDF();
        $fpdi->SetTopMargin(78);
        $fontSize = 11;
        $fpdi->SetFont('Helvetica', '', $fontSize);
        $width = $fpdi->GetPageWidth() - 10; // Width of Current Page
        $height = $fpdi->GetPageHeight() - 10; // Height of Current Page
        $file = public_path('assets/pdf/Filled_Contract_Form.pdf');

        // Get data from database

        foreach ($invigilation_contracts as $contracts) {
            $national_id = $contracts->national_id;
            $invigilation_role_id = $contracts->$roles->$types->name;
            $full_names = $contracts->other_names . ' ' . $contracts->surname;

            $district_id = $contracts->district_id;
            $village = $contracts->village;
            $phone_number = $contracts->phone_number;
            $payment_id = $contracts->payment_id;

            $branch = $contracts->branch;
            $account_number = $contracts->account_number;
            $payable_phone_number = $contracts->payable_phone_number;
            $center_no = $contracts->center_no;
            $amount = $contracts->payment_id;
            $updated_at = $contracts->updated_at;

            $amount = $contracts->payment_id;
            $updated_at = $contracts->updated_at;

            $fpdi->AddPage();
            $fpdi->setSourceFile($file);
            $tplIdx = $fpdi->importPage(1);
            $size = $fpdi->getTemplateSize($tplIdx);

            $fpdi->useTemplate($tplIdx, null, null, $size['width'], $size['height'], true);
            $fpdi->SetFont('Arial');

            //+++++++++++++++++++ map data into existing pdf +++++++++++++++++++
            //  national id
            $fpdi->SetXY(132.6, 64.5);
            $fpdi->Cell(($width * 2) / 4, 6, $national_id, 0);

            //invigilation id
            $fpdi->SetXY(148.1, 81.0);
            $fpdi->Cell(($width * 2) / 4, 6, $invigilation_role_id, 0);

            // other names
            $fpdi->SetXY(11.5, 64.5);
            $fpdi->Cell(($width * 2) / 4, 6, $full_names, 0);

            // district
            $fpdi->SetXY(121.4, 73.4);
            $fpdi->Cell(($width * 2) / 4, 6, $district_id, 0);

            // Other village
            $fpdi->SetXY(25.7, 73.2);
            $fpdi->Cell(($width * 2) / 4, 6, $village, 0);

            // payemnt center
            $fpdi->SetXY(137.7, 156.0);
            $fpdi->Cell(($width * 2) / 4, 6, $center_no, 0);
            // phone number:
            $fpdi->SetXY(81.0, 213.1);
            $fpdi->Cell(($width * 2) / 4, 6, $amount, 0);

            // payemnt method
            $fpdi->SetXY(27.12, 41.5);
            $fpdi->Cell(($width * 2) / 4, 6, $payment_id, 0);

            // payemnt method
            $fpdi->SetXY(45.7, 221.0);
            $fpdi->Cell(($width * 2) / 4, 6, $payment_id, 0);

            // Branch code
            $fpdi->SetXY(148.6, 237);
            $fpdi->Cell(($width * 2) / 4, 6, $branch, 0);

            // other names
            $fpdi->SetXY(17.3, 155.7);
            $fpdi->Cell(($width * 2) / 4, 6, $full_names, 0);

            // Bank name
            // $fpdi->SetXY(32.0,  230.6);
            // $fpdi->Cell($width * 2 / 4, 6, $bank_name, 0);

            // account number
            $fpdi->SetXY(43.1, 237);
            $fpdi->Cell(($width * 2) / 4, 6, $account_number, 0);

            // payable number
            $fpdi->SetXY(47, 244.5);
            $fpdi->Cell(($width * 2) / 4, 6, $payable_phone_number, 0);

            // year
            $fpdi->SetXY(62, 271);
            $fpdi->Cell(($width * 2) / 4, 6, $updated_at, 0);
        }

        $fpdi->Output('Contracts' . '.pdf', 'F');
        header('Content-type: application/pdf');
        header('Content-disposition: attectment; filename = Contracts' . '.pdf');
        readfile('Contracts' . '.pdf');
        unlink('Contracts' . '.pdf');

        exit();
    }
}
}
