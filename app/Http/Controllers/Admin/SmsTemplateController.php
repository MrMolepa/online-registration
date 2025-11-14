<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SmsTemplateController extends Controller
{

    public function getTemplates()
    {
        // For authenticated users only
        $templates = SmsTemplate::where('user_id', auth()->id())->get();
        return response()->json($templates);
    }

    public function saveTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
        $template = SmsTemplate::create([
            'name' => $request->name,
            'content' => $request->content,
            'user_id' => auth()->id()
        ]);

        return response()->json($template);
    }
}
