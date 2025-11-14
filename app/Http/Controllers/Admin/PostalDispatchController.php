<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostalDispatch;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PostalDispatchController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $logs = PostalDispatch::select(['id', 'to', 'reference_no', 'address', 'from', 'date', 'created_at']);            
            return DataTables::of($logs)
                ->addColumn('actions', function($log) {
                    return '
                        <button class="btn btn-primary btn-sm edit-btn" data-url="' . route('admin.front-desk.postal-dispatch.edit', $log->id) . '">Edit</button>
                        <button class="btn btn-danger btn-sm delete-btn" data-url="' . route('admin.front-desk.postal-dispatch.destroy', $log->id) . '">Delete</button>';
                        
                })
                ->editColumn('date', function($log) {
                    return $log->date ? date('d M Y', strtotime($log->date)) : '-';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.front-desk.postal-dispatch.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'to' => 'nullable|string|max:255',
            'reference_no' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'from' => 'nullable|string|max:255',
            'date' => 'required|date',
        ]);

        $log = PostalDispatch::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Postal dispatch created successfully',
            'data' => $log
        ]);
    }

    public function edit(PostalDispatch $postalDispatch)
    {
        return response()->json([
            'success' => true,
            'data' => $postalDispatch,
            'url' => route('admin.front-desk.postal-dispatch.update', $postalDispatch->id)
        ]);
    }

    public function update(Request $request, PostalDispatch $postalDispatch)
    {
        $validated = $request->validate([
            'to' => 'nullable|string|max:255',
            'reference_no' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'from' => 'nullable|string|max:255',
            'date' => 'required|date',
        ]);

        $postalDispatch->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Postal dispatch updated successfully',
            'data' => $postalDispatch
        ]);
    }

    public function destroy(PostalDispatch $postalDispatch)
    {
        $postalDispatch->delete();

        return response()->json([
            'success' => true,
            'message' => 'Postal dispatch deleted successfully'
        ]);
    }
}