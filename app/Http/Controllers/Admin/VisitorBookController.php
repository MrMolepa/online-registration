<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VisitorBookController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $logs = Visitor::select(['id', 'visitor_name', 'meeting_with', 'purpose', 'number_of_person', 'phone', 'date', 'in_time', 'out_time', 'created_at']);            
            return DataTables::of($logs)
                ->addColumn('actions', function($log) {
                    return '
                        <button class="btn btn-primary btn-sm edit-btn" data-url="' . route('admin.front-desk.visitors-book.edit', $log->id) . '">Edit</button>
                        <button class="btn btn-danger btn-sm delete-btn" data-url="' . route('admin.front-desk.visitors-book.destroy', $log->id) . '">Delete</button>';
                        
                })
                ->editColumn('date', function($log) {
                    return $log->date ? date('d M Y', strtotime($log->date)) : '-';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.front-desk.visitors-book.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'purpose' => 'nullable|string|max:255',
            'meeting_with' => 'nullable|string|max:255',
            'visitor_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'number_of_people' => 'nullable|integer|min:1',
            'date' => 'required|date',
            'in_time' => 'nullable|date_format:H:i',
            'out_time' => 'nullable|date_format:H:i',
        ]);

        $log = Visitor::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Visitor created successfully',
            'data' => $log
        ]);
    }

    public function edit(Visitor $visitor)
    {
        return response()->json([
            'success' => true,
            'data' => $visitor,
            'url' => route('admin.front-desk.visitors-book.update', $visitor->id)
        ]);
    }

    public function update(Request $request, Visitor $visitor)
    {
        $validated = $request->validate([
            'purpose' => 'nullable|string|max:255',
            'meeting_with' => 'nullable|string|max:255',
            'visitor_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'number_of_people' => 'nullable|integer|min:1',
            'date' => 'required|date',
            'in_time' => 'nullable|date_format:H:i',
            'out_time' => 'nullable|date_format:H:i',
        ]);

        $visitor->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Visitor updated successfully',
            'data' => $visitor
        ]);
    }

    public function destroy(Visitor $visitor)
    {
        $visitor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Visitor deleted successfully'
        ]);
    }
}