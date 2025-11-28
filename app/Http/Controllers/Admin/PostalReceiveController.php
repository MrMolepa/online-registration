<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostalReceive;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PostalReceiveController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $postals = PostalReceive::select(['id', 'package_name', 'from_title', 'to_title', 'reference_number', 'date_received', 'created_at']);
            
            return DataTables::of($postals)
                ->addColumn('actions', function($postal) {
                    return '
                        <button class="btn btn-primary btn-sm edit-btn" data-url="' . route('admin.front-desk.postal-receive.edit', $postal->id) . '">Edit</button>
                        <button class="btn btn-danger btn-sm delete-btn" data-url="' .  route('admin.front-desk.postal-receive.destroy', $postal->id) . '">Delete</button>';
                        
                })
                ->editColumn('date_received', function($postal) {
                    return $postal->date_received ? date('d M Y', strtotime($postal->date_received)) : '-';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.front-desk.postal-receive.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'package_name' => 'required|string|max:255',
            'from_title' => 'required|string|max:255',
            'to_title' => 'required|string|max:255',
            'reference_number' => 'required|string|max:255|unique:frontdesk_postal_receives,reference_number',
            'date_received' => 'required|date'
        ]);

        // Add the logged-in user's name
        if (auth()->check()) {
            $validated['created_by'] = auth()->user()->name ?? auth()->user()->username ?? auth()->user()->email ?? 'Unknown User';
        }

        $postal = PostalReceive::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Postal receive created successfully',
            'data' => $postal
        ]);
    }


    public function edit(PostalReceive $postalReceive)
    {
        return response()->json([
            'success' => true,
            'url' => route('admin.front-desk.postal-receive.update', $postalReceive->id),
            'data' => $postalReceive
        ]);
    }

    public function update(Request $request, PostalReceive $postalReceive)
    {
        $validated = $request->validate([
            'package_name' => 'required|string|max:255',
            'from_title' => 'required|string|max:255',
            'to_title' => 'required|string|max:255',
            'reference_number' => 'required|string|max:255|unique:frontdesk_postal_receives,reference_number,' . $postalReceive->id,
            'date_received' => 'required|date'
        ]);

        $postalReceive->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Postal receive updated successfully',
            'data' => $postalReceive->fresh()
        ]);
    }

    public function destroy(PostalReceive $postalReceive)
    {
        $postalReceive->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Postal receive deleted successfully'
        ]);
    }
}