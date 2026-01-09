<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $enquiries = Enquiry::select(['id', 'name', 'email', 'phone', 'enquiry_date', 'description', 'is_active', 'created_at']);
            
            return DataTables::of($enquiries)
                ->addColumn('status_badge', function($enquiry) {
                    $class = $enquiry->is_active ? 'success' : 'danger';
                    $text = $enquiry->is_active ? 'Active' : 'Inactive';
                    return '<span class="label label-'.$class.'">'.$text.'</span>';
                })
                ->addColumn('actions', function($enquiry) {
                    return '
                        <button class="btn btn-info btn-sm view-btn" data-url="' . route('admin.front-desk.enquiry.show', $enquiry->id) . '" title="View">
                            <i class="fa fa-eye"></i>
                        </button>
                        <button class="btn btn-primary btn-sm edit-btn" data-url="' . route('admin.front-desk.enquiry.edit', $enquiry->id) . '" title="Edit">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm delete-btn" data-url="' . route('admin.front-desk.enquiry.destroy', $enquiry->id) . '" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>';
                })
                ->editColumn('enquiry_date', function($enquiry) {
                    return $enquiry->enquiry_date ? date('d M Y', strtotime($enquiry->enquiry_date)) : '-';
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        }

        return view('admin.front-desk.enquiry.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'enquiry_date' => 'required|date',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

          // Add the logged-in user's name
        if (auth()->check()) {
            $validated['created_by'] = auth()->user()->name ?? auth()->user()->username ?? auth()->user()->email ?? 'Unknown User';
        }


        // Handle checkbox value
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $enquiry = Enquiry::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Enquiry created successfully',
            'data' => $enquiry
        ]);
    }

    public function show(Enquiry $enquiry)
    {
        return response()->json([
            'success' => true,
            'data' => $enquiry
        ]);
    }

    public function edit(Enquiry $enquiry)
    {
        return response()->json([
            'success' => true,
            'url' => route('admin.front-desk.enquiry.update', $enquiry->id),
            'data' => $enquiry
        ]);
    }

    public function update(Request $request, Enquiry $enquiry)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'enquiry_date' => 'required|date',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

        // Handle checkbox value
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $enquiry->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Enquiry updated successfully',
            'data' => $enquiry->fresh()
        ]);
    }

    public function destroy(Enquiry $enquiry)
    {
        $enquiry->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Enquiry deleted successfully'
        ]);
    }
}