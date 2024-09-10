<?php

namespace App\Http\Controllers\application;

use App\Models\InvigilationPaymentMethod;
use App\Models\InvigilatorProfile;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
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
            $payment_method_attributes = array_diff(array_keys($payment_method->getAttributes()), ['created_at', 'updated_at']);
            foreach ($payment_method_attributes as $key => $payment_method_attribute) {
                if ($payment_method->{$payment_method_attribute} == 1) {
                    $name = substr($payment_method_attribute, 3);
                    $CapName = ucfirst(str_replace('_', ' ', $name));

                    $payment_method_html .= " <div class='form-group col-sm-10 row'>
                                                <label id='name-label' class='col-sm-4' for='name'>$CapName</label>
                                                <input type='text' name='$name'id='$name'
                                                    value=''
                                                    class='form-control col-sm-6'>
                                            </div>";
                }
            }

            return response()->json(['payment_methods' =>  $payment_method_html]);
        }

        $token = $request->token;
        $invigilator = InvigilatorProfile::with('invigilation_role.invigilation_type')->where('token', $token)->first();
        if (!$invigilator) {
            //redirect them anywhere you want if the token does not exist.
            abort(403, 'You do not have access to fill this contract form');
            // return redirect()->route('sponsor.login');

        } else {


            $url = route('applications.update', $token);
            $geturl = route('applications.index', $token);
            $payment_methods = InvigilationPaymentMethod::get();
            return view('applications.invigilator.index', compact('invigilator', 'url', 'geturl', 'payment_methods'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $token)
    {
        $validator = Validator::make($request->all(), []);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->getMessageBag()->toArray()]);
        } else {

            $invigilation = InvigilatorProfile::where('token', $token)->first();
            if ($invigilation) {
                $invigilation->gender = $request->gender;
                $invigilation->date_of_birth =  date('Y-m-d', strtotime($request->date_of_birth));
                $invigilation->qualification = $request->qualification;


                $invigilation->bank_name = $request->bank_name;
                $invigilation->branch = $request->branch;
                $invigilation->account_number = $request->account_number;
                $invigilation->mpesa_phone_number = $request->mpesa_phone_number;
                $invigilation->ecocash_phone_number = $request->ecocash_phone_number;

                $invigilation->save();
                return response()->json(['success' => 'Application successfully submitted']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Model not found']);
    }
}
