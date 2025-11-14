<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SmsController extends Controller
{
     public function index()
    {
        return view('admin.sms.index');
    }

    public function getContacts()
    {
        // In a real app, you'd fetch from database
        $contacts = [
            ['name' => 'John Doe', 'phone' => '+1234567890', 'group' => 'Family'],
            ['name' => 'Jane Smith', 'phone' => '+1987654321', 'group' => 'Work'],
            ['name' => 'Mike Johnson', 'phone' => '+1122334455', 'group' => 'Friends']
        ];

        return response()->json($contacts);
    }

    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'required|string',
            'message' => 'required|string|max:160',
            'is_unicode' => 'boolean',
            'scheduled_at' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $sms = Sms::create([
            'recipients' => $request->recipients,
            'message' => $request->message,
            'is_unicode' => $request->is_unicode ?? false,
            'scheduled_at' => $request->scheduled_at,
            'status' => $request->scheduled_at ? 'scheduled' : 'pending'
        ]);

        // Here you would typically call your SMS gateway API
        // For demo, we'll just simulate success
        // $this->sendToGateway($sms);

        return response()->json([
            'success' => true,
            'message' => 'SMS sent successfully!',
            'data' => $sms
        ]);
    }

    protected function sendToGateway($sms)
    {
        // Implement your actual SMS gateway integration here
        // This is just a simulation
        if (!$sms->scheduled_at) {
            $sms->update(['status' => 'sent']);
        }
    }
}
