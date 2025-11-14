<?php

namespace App\Http\Controllers\application;

use App\Models\District;
use App\Models\InvigilationPaymentMethod;
use App\Models\InvigilationStatus;
use App\Models\InvigilatorProfile;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Libraries\fpdfcertificate\exFPDF;
use Illuminate\Http\Request;

class InvigilatorProfileController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->payment_method_id;
            $payment_method = InvigilationPaymentMethod::find($id);
            $payment_method_html = "";
            $payment_method_attributes = array_diff(array_keys($payment_method->getAttributes()), ['id','name','description','created_at', 'updated_at']);
            foreach ($payment_method_attributes as $key => $payment_method_attribute) {
                if ($payment_method->{$payment_method_attribute} == 1) {
                    $name = substr($payment_method_attribute, 3);
                    $CapName = ucfirst(str_replace('_', ' ', $name));
                    $payment_method_html .= " <div class='form-group col-12'>
                                                <label id='name-label' for='name'>$CapName</label>
                                                <input type='text' name='$name'id='$name'
                                                    value=''
                                                    class='form-control'>
                                                </div>";
                }
            }
            return response()->json(['payment_methods' =>  $payment_method_html]);
        }

        $token = $request->token;
        $invigilator = InvigilatorProfile::with('invigilation_role.invigilation_type', 'invigilation_status', 'invigilator_district')
           ->whereHas('invigilation_status', function($q){
                $q->where('status', '!=', 0);
            })
          ->where('token', $token)->first();

        $invigilator_districts = District::get();
        if (!$invigilator) {
            //redirect them anywhere you want if the token does not exist.
            abort(403, 'You do not have access to fill this contract form');
            // return redirect()->route('sponsor.login');

        } else {
            $laststatus = InvigilationStatus::where('status', '=', 1)->orderBy('order_status', 'DESC')->first();
            if ($laststatus->id == $invigilator->progress_status_id) {
                abort(403, 'You have already submitted your contract form');
            }

            if ($request->has('declined')) {
                $url = route('applications.declined', $token);
                return view('applications.invigilator.declined', compact('url'));
            }
            $url = route('applications.update', $token);
            $geturl = route('applications.index', $token);
            $contracturl=route('applications.exportSinglePdf', $invigilator->id);
            $payment_methods = InvigilationPaymentMethod::get();
            return view('applications.invigilator.index', compact('invigilator', 'url', 'geturl', 'payment_methods', 'invigilator_districts','contracturl'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $token)
    {
        $validator = Validator::make($request->all(), [
            'date_of_birth' => 'required',
            'gender' => 'required',
            'qualification' => 'required',
            'district_id' => 'required',
            'village' => 'required',
            'payment_id' => 'required',
            'account_number' => 'required_if:payment_id, 1|regex:/^([0-9\s\-\+\(\)]*)$/|min:11|max:15',
            'bank_name' => 'required_if:payment_id, 1',
            'branch' => 'required_if:payment_id, 1',
            'payable_phone_number' => 'required_if:payment_id, 2 || required_if:payment_id, 3|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:8',
        ],
        ['payment_id.required' => 'Payment method is required','account_number.required_if' => 'Account number is required',
        'bank_name.required_if' => 'Bank name is required',
        'branch.required_if' => 'Branch code is required',
        'payable_phone_number.required_if' => 'Phone number is required'],
    );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        } else {
            $invigilation = InvigilatorProfile::where('token', $token)->first();
            $status = InvigilationStatus::where('status', '=', 1)
                ->where('id', '!=',  $invigilation->progress_status_id)
                ->orderBy('order_status', 'ASC')->first();
            if ($invigilation) {
                $invigilation->gender = $request->gender;
                $invigilation->date_of_birth =  date('Y-m-d', strtotime($request->date_of_birth));
                $invigilation->qualification = $request->qualification;
                $invigilation->payment_id = $request->payment_id;
                $invigilation->payable_phone_number = $request->has('payable_phone_number')?$request->payable_phone_number:'';
                $invigilation->account_number = $request->has('account_number')?$request->account_number:'';
                $invigilation->bank_name = $request->has('bank_name')?$request->bank_name:'';
                $invigilation->branch = $request->has('branch')?$request->branch:'';
                $invigilation->tin_number = $request->has('tin_number')?$request->tin_number:'';
                $invigilation->district_id = $request->district_id;
                $invigilation->village = $request->village;
                $invigilation->progress_status_id = $status->id;
                $invigilation->save();
                return response()->json(['success' => 'Application successfully submitted']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Model not found']);
    }
    // Decline
    public function declined(Request $request, string $token)
    {
        $validator = Validator::make($request->all(), [
            'declined' => 'required|string'
        ]);

        if ($validator->fails()) {
            return view('applications.invigilator.index');
        } else {
            $invigilation = InvigilatorProfile::where('token', $token)->first();


            $status = InvigilationStatus::where('status', '=', 0)
                ->where('id', '!=',  $invigilation->progress_status_id)
                ->orderBy('order_status', 'ASC')->first();


            if ($invigilation) {
                $invigilation->progress_status_id = $status->id;
                $invigilation->save();
                return response()->json(['success' => 'Application declined']);
            }
        }

        return view('applications.invigilator.declined');
    }
    public function exportSinglePdf(string $id)
    {

        $invigilation_contracts = InvigilatorProfile::with('invigilation_role', 'invigilation_role.invigilation_type', 'invigilation_role.invigilator_paymentamount', 'invigilator_district', 'invigilator_payment', 'invigilation_status')->find($id);
        if (($invigilation_contracts->invigilation_status->name) == 'Accepted') {
            if (!empty($invigilation_contracts)) {
                // if has value then return
                $fpdi = new exFPDF();
                $fpdi->SetTopMargin(78);
                $fontSize = 11;
                $fpdi->SetFont('Helvetica', '',  $fontSize);
                $width = $fpdi->GetPageWidth() - 10;  // Width of Current Page
                $height = $fpdi->GetPageHeight() - 10; // Height of Current Page
                //$file = public_path("assets/pdf/Filled_Contract_Form.pdf");
                 $file = "/home/ecol/ecol.coltech.co.za/assets/pdf/Filled_Contract_Form.pdf";

                // Get data from database
                $national_id = $invigilation_contracts->national_id;

                $invigilation_role_id = $invigilation_contracts->invigilation_role->invigilation_type->name;

                $full_names = $invigilation_contracts->other_names . "  " . $invigilation_contracts->surname;

                $district_id = $invigilation_contracts->invigilator_district->district_name;

                $village = $invigilation_contracts->village;

                $phone_number = $invigilation_contracts->phone_number;

                $payment_id = $invigilation_contracts->invigilator_payment->name;

                $branch = $invigilation_contracts->branch;

                $bank_name = $invigilation_contracts->bank_name;

                $account_number = $invigilation_contracts->account_number;

                $payable_phone_number = $invigilation_contracts->payable_phone_number;

                $center_no = $invigilation_contracts->center_no;

                $updated_at = $invigilation_contracts->updated_at;
                $issessionbased = $invigilation_contracts->invigilation_role->is_sessions;

                if ($issessionbased == '1') {
                    $issessionbased = "per session";
                } else {
                    $issessionbased = " ";
                }
                $amount = number_format($invigilation_contracts->invigilation_role->invigilator_paymentamount->amount, 2, '.', '') . ' ' . $issessionbased;

                $updated_at = $invigilation_contracts->updated_at;



                $fpdi->AddPage();
                $fpdi->setSourceFile($file);
                $tplIdx = $fpdi->importPage(1);
                $size = $fpdi->getTemplateSize($tplIdx);


                $fpdi->useTemplate($tplIdx, null, null, $size['width'], $size['height'], true);
                $fpdi->SetFont('Arial');

                //+++++++++++++++++++ map data into existing pdf +++++++++++++++++++
                //  national id
                $fpdi->SetXY(132.6,  64.5);
                $fpdi->Cell($width * 2 / 4, 6,  $national_id, 0);



                $invigilator = array(
                    'invigilator' =>   $invigilation_contracts->id,
                    'national_id' =>   $national_id,
                    'full_names' =>  $full_names,
                    'center_no ' => $center_no,
                );

                $decodedImg = grCodeGenerator($national_id, $invigilator);
                $pic = 'data://text/plain;base64,' .  $decodedImg;

                $fpdi->Image($pic, 170, 38, 32, 29, 'png');

                // invigilation id
                $fpdi->SetXY(148.1,  80.2);
                $fpdi->Cell($width * 2 / 4, 6, $invigilation_role_id, 0);

                // other names
                $fpdi->SetXY(13,  64.5);
                $fpdi->Cell($width * 2 / 4, 6, $full_names, 0);

                // district
                $fpdi->SetXY(121.4,  72.3);
                $fpdi->Cell($width * 2 / 4, 6, $district_id, 0);

                // Other village
                $fpdi->SetXY(25.7,  72.3);
                $fpdi->Cell($width * 2 / 4, 6, $village, 0);

                // center
                $fpdi->SetXY(138.8,  155.5);
                $fpdi->Cell($width * 2 / 4, 6, $center_no, 0);
                // phone number:
                $fpdi->SetXY(84.0,  212.9);
                $fpdi->Cell($width * 2 / 4, 6,  $amount, 0);

                // payemnt method
                $fpdi->SetXY(47.7,  220.9);
                $fpdi->Cell($width * 2 / 4, 6, $payment_id, 0);

                // Branch code
                $fpdi->SetXY(148.6,  236.7);
                $fpdi->Cell($width * 2 / 4, 6, $branch, 0);

                // other names
                $fpdi->SetXY(18.3,  155.5);
                $fpdi->Cell($width * 2 / 4, 6, $full_names, 0);

                // Bank name
                $fpdi->SetXY(32.0,  228.75);
                $fpdi->Cell($width * 2 / 4, 6, $bank_name, 0);


                // account number
                $fpdi->SetXY(43.1,  236.7);
                $fpdi->Cell($width * 2 / 4, 6,  $account_number, 0);

                // payable number
                $fpdi->SetXY(47,  244.6);
                $fpdi->Cell($width * 2 / 4, 6, $payable_phone_number, 0);

                // year
                $fpdi->SetXY(100,  271);
                $fpdi->Cell($width * 2 / 4, 6,  $updated_at, 0);


                // signed
                $fpdi->SetXY(41,  271);
                $fpdi->Cell($width * 2 / 4, 6, $full_names, 0);

                $fpdi->Output("Invigilation Contract"  . ".pdf", "F");
                header("Content-type: application/pdf");
                header("Content-disposition: attectment; filename = Invigilation Contract" . '.pdf');
                readfile("Invigilation Contract" . ".pdf");
                unlink("Invigilation Contract" . '.pdf');
                exit;
            } else {
                return redirect('pages/404')->with('error', "Contract Printing failed");
            }
        } else {
            return response()->json(['error' => 'Contract is not signed or it is declined.']);
        }
    }
}
