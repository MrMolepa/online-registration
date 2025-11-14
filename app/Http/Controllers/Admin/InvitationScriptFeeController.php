<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InvitationMail;
use App\Models\CenterCandidate;
use App\Models\Invitation;
use App\Models\InvitationRecipient;
use App\Models\InvitationRecipientField;
use App\Models\InvitationRole;
use App\Models\InvitationScriptFee;
use App\Models\Session;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InvitationScriptFeeController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $invitationScriptFees = InvitationScriptFee::with([
                'subject',
            ]);
            // Filter by columns e

            if ($request->filled('financial_year')) {
                $invitationScriptFees->where('financial_year', $request->financial_year);
            }
            // if ($request->filled('level')) {
            //     $q->where('level', $request->level);
            // }
            if ($request->filled('session')) {
                $invitationScriptFees->where('session', $request->session);
            }



            $invitationScriptFees = $invitationScriptFees->get();
            return datatables()->of($invitationScriptFees)
                ->addColumn('action', function ($invitationScriptFee) {
                    $edit = '<a href="' . route('admin.invitations.script-fee.edit', $invitationScriptFee->component_code) . '" class="btn btn-xs btn-primary edit-fee">Edit</a>';
                    $delete = '<a href="' . route('admin.invitations.script-fee.destroy', $invitationScriptFee->component_code) . '" class="btn btn-xs btn-danger delete-fee">Delete</a>';
                    return $edit . ' ' . $delete;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
    }


    public function store(Request $request)
    {
        $financial_year = date('m') > 2 ?  date('Y') . '-' . date('Y') + 1 : date('y') - 1 . '-' . date('Y');
        $validator = Validator::make($request->all(), [
            'subject_code' => ['required', 'string', 'max:50'],
            'component_no' => [
                'required',
                'string',
                'max:50',
                // Ensure combination is unique
                Rule::unique('invitation_script_fee')->where(function ($query) use ($request) {
                    return $query->where('subject_code', $request->subject_code)
                        ->where('component_no', $request->component_no);
                }),
            ],
            'script_fee' => 'required|numeric|min:0|max:999999.99',
            'session' => 'required|string|in:January,February,March,April,May,June,July,August,September,October,November,December',
            'financial_year' => "required|string|max:20|in:$financial_year",
        ]);

        // Handle errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        InvitationScriptFee::create($request->all());
        return response()->json(['success' => 'Fee created successfully']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $fee = InvitationScriptFee::with([
            'subject',
        ])->where('component_code', $id)
            ->findOrFail($id);
        $url = route('admin.invitations.script-fee.update', $id);
        return response()->json(['fee' => $fee, 'url' => $url]);
    }



    public function update(Request $request, $id)
    {
        $financial_year = date('m') > 2 ?  date('Y') . '-' . date('Y') + 1 : date('y') - 1 . '-' . date('Y');
        $validator = Validator::make($request->all(), [
            'subject_code' => ['required', 'string', 'max:50'],
            'component_no' => [
                'required',
                'string',
                'max:50',
                // Ensure combination is unique
                Rule::unique('invitation_script_fee')->where(function ($query) use ($request) {
                    return $query->where('subject_code', $request->subject_code)
                        ->where('component_no', $request->component_no);
                })->ignore($id, 'component_code'),
            ],
            'script_fee' => 'required|numeric|min:0|max:999999.99',
            'session' => 'required|string|in:January,February,March,April,May,June,July,August,September,October,November,December',
            'financial_year' => "required|string|max:20|in:$financial_year",
        ]);

        // Handle errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        };


        $fee = InvitationScriptFee::with([
            'subject',
        ])->where('component_code', $id)
            ->findOrFail($id);



        // 2️⃣ Update existing fee
        $fee->update([
            'subject_code' => $request->subject_code,
            'component_no'  => $request->component_no,
            'script_fee' => $request->script_fee,
            'session' => $request->session,
            'financial_year' => $request->financial_year,

        ]);
        return response()->json(['success' => 'Fee updated successfully']);
    }


    public function destroy($id)
    {
        $fee = InvitationScriptFee::with([
            'subject',
        ])->where('component_code', $id)
            ->first();
        // Delete invitation
        $fee->delete();

        return response()->json(['success' => 'Invitation deleted successfully!']);
    }
}
