<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CenterCandidate;
use App\Models\Invitation;
use App\Models\InvitationRecipient;
use App\Models\InvitationRecipientField;
use App\Models\InvitationRole;
use App\Models\Session;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InvitationRecipientController extends Controller
{


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $recipient = InvitationRecipient::find($id);
        $url = route('admin.invitations.recipients.update', $id);
        return response()->json(['recipient' => $recipient, 'url' => $url]);
    }




    public function update(Request $request, $id)
    {
        // 1️⃣ Validate recipient basic info
        $basicRules = [
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'phone_number' => 'required|numeric|regex:/^[0-9]{8}$/',
            'national_id'     => ['nullable', 'numeric', 'regex:/^(\d{11}|\d{12}|\d{13})$/'],
            'email'        => [
                'required',
                'email',
                Rule::unique('invitation_recipients')->ignore($id) // 👈 ignore current record
            ],
        ];

        $validator = Validator::make($request->all(), $basicRules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        // 2️⃣ Find recipient
        $recipient = InvitationRecipient::find($id);


        // 3️⃣ Update recipient
        $recipient->update([
            'email'        => $request->email,
            'first_name'   => $request->first_name,
            'last_name'    => $request->last_name,
            'phone_number' => $request->phone_number,
            'national_id'  => $request->national_id,
        ]);

        return response()->json([
            'success' => 'Recipient invitation updated successfully!',
        ]);
    }


    public function destroy($id)
    {
        $recipient = InvitationRecipient::with('invitations')->find($id);
        if (!$recipient) {
            return response()->json(['error' => 'Recipient not found.']);
        }

        // Check if recipient has invitations
        if ($recipient->invitations->isNotEmpty()) {
            return response()->json([
                'error' => 'Cannot delete recipient with invitations.'
            ]);
        }
        // Safe to delete
        $recipient->delete();
        return response()->json([
            'success' => 'Recipient deleted successfully!'
        ]);
    }













    //     public function showResponse($token)
    // {
    //     $invitation = Invitation::where('token', $token)->with('role')->firstOrFail();

    //     // Get default fields from DB
    //     $defaultFields = DefaultField::all()->toArray();

    //     $roleFields = $invitation->role->fields ?? [];

    //     // Merge default + role fields
    //     $allFields = array_merge($defaultFields, $roleFields);

    //     return view('invitations.response', [
    //         'invitation' => $invitation,
    //         'roleFields' => $allFields
    //     ]);
    // }
}
