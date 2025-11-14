<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Libraries\fpdfcertificate\exFPDF;
use App\Libraries\SMS\SmsApi;
use App\Models\District;
use App\Models\InvigilationPaymentMethod;
use App\Models\Invitation;
use App\Models\InvitationPayment;
use App\Models\InvitationRecipient;
use App\Models\InvitationScriptFee;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use PhpParser\Node\Expr\Isset_;

class InvitationResponseController extends Controller
{
    public function showResponse(Request $request, $token)
    {

        $invitation = Invitation::where('response_token', $token)
            ->with(['payment', 'role.fields', 'recipient.center.principal'])
            ->firstOrFail();
        $allBanks = banks();

        // Map responses for easy lookup
        $downloadable = !is_null($invitation->workflowInstance) && $invitation->status == 'complete' && $invitation->workflowInstance->status == 'completed';


        // Get all fields for the role
        $allFields = $invitation->role->fields
            ->where('source_type', 'custom')
            ->transform(function ($field) use ($invitation) {
                if (!empty($field->source) && $field->source !== 'manual') {
                    // Dynamic DB source
                    if (Schema::hasTable($field->source)) {
                        $keyColumn   = $field->key_column   ?? 'id';
                        $valueColumn = $field->value_column ?? 'name';

                        $query = DB::table($field->source)->select([$keyColumn, $valueColumn]);

                        switch ($field->source) {
                            case 'subjects':
                                if ($invitation->level) {
                                    $level = Level::where('level', $invitation->level)->first();
                                    if ($level) {
                                        $query->where('level', $level->id);
                                    }
                                }
                                break;

                            case 'centers':
                                if ($invitation->level) {
                                    $query->where('level', $invitation->level);
                                }
                                break;
                        }

                        $field->options = $query->pluck($valueColumn, $keyColumn)->toArray();
                    } else {
                        $field->options = [];
                    }
                } else {
                    // Manual options
                    $field->options = $field->options ? json_decode($field->options, true) : [];
                }

                return $field;
            });








        if ($request->ajax()) {
            $id = $request->payment_method_id;
            $payment_method = InvigilationPaymentMethod::find($id);
            $payment_method_html = "";
            $payment_method_attributes = array_diff(array_keys($payment_method->getAttributes()), ['id', 'name', 'description', 'created_at', 'updated_at']);

            foreach ($payment_method_attributes as $key => $payment_method_attribute) {
                if ($payment_method->{$payment_method_attribute} == 1) {
                    $name    = substr($payment_method_attribute, 3); // remove prefix
                    $CapName = ucfirst(str_replace('_', ' ', $name));
                    $value   = optional($invitation->payment)->{$name} ?? '';
                    $input = '';
                    if ($name === 'bank_name') {
                        $input = "<div class='form-group col-12'>
                                    <label>$CapName</label>
                                    <select name='$name' id='$name' class='form-control'>
                                    <option value=''>Select Bank</option>";

                        foreach ($allBanks as $key => $bank) {
                            $selected = ($value == $key) ? 'selected' : '';
                            $input .= "<option value='$key' $selected>{$bank['label']}</option>";
                        }

                        $input .= "</select>
                </div>";
                    } else {
                        $input = "<div class='form-group col-12'>
                    <label>$CapName</label>
                    <input type='text' name='$name' id='$name' value='$value' class='form-control'>
                  </div>";
                    }

                    $payment_method_html .= $input;
                }
            }
            return response()->json(['payment_methods' =>  $payment_method_html]);
        }
        // Map responses for easy lookup
        $responses = $invitation->invitationFields()
            ->whereNotNull('field_value')
            ->where('field_value', '!=', '')
            ->pluck('field_value', 'field_id',)
            ->toArray();

        if (count($responses) < 2) {
            abort(403, 'The contract form is not ready. Please contact the relevant department first.');
        }

        $payment_methods = InvigilationPaymentMethod::where('name', 'BANK')->get();
        $url = route('applications.invitation.submitResponse', $token);
        $paymentUrl = route('applications.invitation.response', $token);
        $contractUrl = route('applications.invitation.generatePdf', $token);
        $districts = District::get();




        return view('applications.invitation', [
            'invitation' => $invitation,
            'allFields' =>  $allFields,
            'url' => $url,
            'downloadable' => $downloadable,
            'paymentUrl' => $paymentUrl,
            'responses' => $responses,
            'contractUrl' => $contractUrl,
            'payment_methods' => $payment_methods,
            'districts' => $districts
        ]);
    }



    public function submitResponse(Request $request, string $token)
    {
        $invitation = Invitation::where('response_token', $token)
            ->with(['role.fields', 'recipient'])
            ->firstOrFail();
        $step = $request->input('step');


        $recipient = $invitation->recipient;

        switch ($step) {
            case 1: // Personal Info + Dynamic Fields
                // Step 1: validate core recipient info
                $coreData = $request->validate([
                    'national_id' => ['required', 'numeric', 'regex:/^(\d{11}|\d{12}|\d{13})$/'],
                    'village'     => 'required|string|max:255',
                    'district'    => 'required|string|max:255',
                ]);

                // Step 1: dynamic fields validation
                $dynamicRules = [];

                foreach ($request->input('fields', []) as $key => $val) {
                    $dynamicRules["fields.$key"] = 'required'; // adjust by type
                }

                $dynamicData = $request->validate($dynamicRules);

                // Save dynamic fields in pivot table

                foreach ($dynamicData['fields'] ?? [] as $key => $value) {
                    DB::table('invitation_recipient_fields')->updateOrInsert(
                        [
                            'invitation_id' => $invitation->id,
                            'recipient_id'  => $recipient->id,
                            'field_key'     => $key,
                        ],
                        ['field_value' => $value]
                    );
                }

                // Update recipient with core data only
                $recipient->update($coreData);
                break;

            case 2: // Payment Info
                $paymentData = $request->validate([
                    'payment_id'      => 'required',
                    'account_number'  => 'required_if:payment_id,1',
                    'bank_name'       => 'required_if:payment_id,1',
                    'branch'          => 'required_if:payment_id,1',
                    'tin_number'      => 'required|regex:/^\d{9}-\d$/',
                ]);

                InvitationPayment::updateOrCreate(
                    [
                        'invitation_id' => $invitation->id,
                        'payment_id'    => $paymentData['payment_id'],
                    ],
                    $paymentData
                );

                break;
            case 3: // Contract Upload
                // Validate input
                $data = $request->validate([
                    'terms'                   => 'required',
                    'principal_last_name'     => 'required|string|max:255',
                    'principal_first_name'    => 'required|string|max:255',
                    'principal_phone_number'  => [
                        'required',
                        'numeric',
                        'regex:/^[0-9]{8}$/',
                        // Rule::unique('invitation_recipients', 'phone_number')
                        //     ->where(fn($query) => $query->where('type', 'principal'))
                    ],
                    'principal_email' => [
                        'required',
                        'email',
                        'max:255',
                        // Rule::unique('invitation_recipients', 'email')
                        //     ->where(fn($query) => $query->where('type', 'principal'))
                    ],
                ]);
                $type = 'principal';
                // Update existing record or create new one
                $principal = InvitationRecipient::updateOrCreate(
                    [
                        'email' => $data['principal_email'], // find by email
                        'type'  => $type,
                        'center_no'   =>  $invitation->center_no,
                    ],
                    [
                        'first_name'   => $data['principal_first_name'],
                        'last_name'    => $data['principal_last_name'],
                        'phone_number' => $data['principal_phone_number'],
                    ]
                );



                if ($request->hasFile('signed_document')) {
                    $path = $request->file('signed_document')->store('signed_contracts');
                    $invitation->signed_document = $path;
                }

                $invitation->responded_at = now();
                $invitation->status = 'complete'; // fixed typo
                $invitation->save();

                $url = route('center.invitations.index', 'markers');
                $msisdn =  $principal->phone_number;
                $message = "Good Day $principal->first_name $principal->last_name,\n A teacher ($recipient->first_name $recipient->last_name) has applied for $invitation->session  $invitation->financial_year marking with ECoL.\nLogin to approve or decline the request: $url";
                SmsApi::message($msisdn, $message);
                break;
            default:
                return response()->json(['error' => 'Invalid step'], 400);
        }
        return response()->json(['success' => true, 'next_step' => $step + 1]);
    }
    public function generatePdf($token)
    {
        $invitation = Invitation::where('response_token', $token)
            ->with([
                'role.fields',
                'payment',
                'recipient',
                'invitationFields'
            ])
            ->firstOrFail();
        $pdf = new exFPDF(); // or exFPDF if you extended FPDF
        $pdf->SetFont('Helvetica', 'I', 12);
        $templatePath = storage_path('app/' . $invitation->role->contract_template);
        // Load PDF template
        $pageCount = $pdf->setSourceFile($templatePath);
        // Designer canvas size (must match what you used in PDF.js)
        $canvasWidthPx = 892;
        $canvasHeightPx = 1262;
        // Loop through each page
        for ($i = 1; $i <= $pageCount; $i++) {
            $tplId = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($tplId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tplId);


            $decodedArray = array(
                'national_id' =>   $invitation->recipient->national_id,
                'full_names' =>   $invitation->recipient->first_name . ' ' . $invitation->recipient->last_name,
                'Role' => $invitation->role->name,
            );

            $decodedImg = grCodeGenerator($invitation->recipient->national_id,   $decodedArray);
            $pic = 'data://text/plain;base64,' . $decodedImg;

            $pdf->Image($pic, 170, 38, 32, 29, 'png');

            // Scaling factors
            $scaleX = $size['width'] / $canvasWidthPx;
            $scaleY = $size['height'] / $canvasHeightPx;

            // Overlay fields on this page
            foreach ($invitation->role->fields as $field) {
                if (empty($field->positions)) continue;
                // --- get value once ---
                switch ($field->source_type) {
                    case 'recipient':
                        $value = $invitation->recipient->{$field->name} ?? '';
                        break;
                    case 'payment':
                        $value = $invitation->payment->{$field->name} ?? '';
                        break;
                    case 'script_fee':
                        $invitation_s = $invitation->invitationFields
                            ->whereIn('field_key', ['subject_code', 'component_no'])->pluck('field_value', 'field_key')->toArray();
                        $component_code = str_pad($invitation_s['subject_code'], 4, '0', STR_PAD_LEFT) .
                            str_pad($invitation_s['component_no'], 2, '0', STR_PAD_LEFT);
                        $value = 'No fee';
                        if (!is_null($field->key_column)) {
                            $value = InvitationScriptFee::where('component_code', '=', "$component_code")
                                ->value($field->key_column) ?? '';
                        }

                        break;
                    default: // custom field response
                        $response = $invitation->invitationFields
                            ->where('field_id', $field->id)
                            ->first();
                        $value = $response ? $response->field_value : '';
                        break;
                }

                if (!$value) continue;

                // --- loop through all saved positions ---
                foreach ($field->positions as $pos) {
                    if (!isset($pos->page) || $pos->page != $i) {
                        continue;
                    }
                    $x =  $pos['pos_x'] * $scaleX;
                    $y = $pos['pos_y'] * $scaleY;



                    $w = isset($pos['width']) ? $pos['width'] * $scaleX : 0;
                    $h = isset($pos['height']) ? $pos['height'] * $scaleY : 0;
                    $pdf->SetXY($x, $y);
                    if ($w && $h) {
                        $pdf->MultiCell($w, 5, $value);
                    } else {
                        $pdf->Write(0, $value);
                    }
                }
            }
        }

        // 🔹 Ensure invitations folder exists
        $directory = storage_path('app/invitations');
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // 🔹 Save file
        $outputPath = $directory . '/Contract_' . $invitation->recipient->national_id . '.pdf';
        $pdf->Output($outputPath, 'F');

        // 🔹 Return as response (inline view)
        return response()->file($outputPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invitation_' . $invitation->recipient->national_id . '.pdf"',
        ]);
    }
}
