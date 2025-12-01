<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhoneCallLog;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PhoneCallLogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $logs = PhoneCallLog::select(['id', 'name', 'phone', 'date', 'call_type', 'next_follow_up_date', 'call_duration', 'description', 'note', 'created_at']);
            
            return DataTables::of($logs)
                ->addColumn('call_type_badge', function($log) {
                    $class = $log->call_type === 'Incoming' ? 'primary' : 'success';
                    return '<span class="label label-'.$class.'">'.$log->call_type.'</span>';
                })
                ->addColumn('actions', function($log) {
                    return '
                        <button class="btn btn-primary btn-sm edit-btn" data-url="' . route('admin.front-desk.phone-call-log.edit', $log->id) . '">Edit</button>
                        <button class="btn btn-danger btn-sm delete-btn" data-url="' . route('admin.front-desk.phone-call-log.destroy', $log->id) . '">Delete</button>';
                        
                })
                ->editColumn('date', function($log) {
                    return $log->date ? date('d M Y', strtotime($log->date)) : '-';
                })
                ->editColumn('next_follow_up_date', function($log) {
                    return $log->next_follow_up_date ? date('d M Y', strtotime($log->next_follow_up_date)) : '-';
                })
                ->rawColumns(['call_type_badge', 'actions'])
                ->make(true);
        }

        return view('admin.front-desk.phone-call-log.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'next_follow_up_date' => 'nullable|date',
            'call_duration' => 'nullable|string|max:50',
            'note' => 'nullable|string',
            'call_type' => 'required|in:Incoming,Outgoing'
        ]);

        // Add the logged-in user's name
        if (auth()->check()) {
            $validated['created_by'] = auth()->user()->name ?? auth()->user()->username ?? auth()->user()->email ?? 'Unknown User';
        }

        $log = PhoneCallLog::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Phone call log created successfully',
            'data' => $log
        ]);
    }

    public function edit(PhoneCallLog $phoneCallLog)
    {
        return response()->json([
            'success' => true,
            'url' => route('admin.front-desk.phone-call-log.update', $phoneCallLog->id),
            'data' => $phoneCallLog
        ]);
    }

    public function update(Request $request, PhoneCallLog $phoneCallLog)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'next_follow_up_date' => 'nullable|date',
            'call_duration' => 'nullable|string|max:50',
            'note' => 'nullable|string',
            'call_type' => 'required|in:Incoming,Outgoing'
        ]);

        $phoneCallLog->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Phone call log updated successfully',
            'data' => $phoneCallLog->fresh()
        ]);
    }

    public function destroy(PhoneCallLog $phoneCallLog)
    {
        $phoneCallLog->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Phone call log deleted successfully'
        ]);
    }
}