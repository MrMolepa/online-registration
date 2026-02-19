<?php

namespace App\Http\Controllers\Admin\FunWalk;

use App\Http\Controllers\Controller;
use App\Models\FunWalk;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FunWalkController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $funWalks = FunWalk::select(['id', 'title', 'date', 'location', 'price', 'description', 'status']);

            return DataTables::of($funWalks)
                ->addColumn('status_badge', function ($funWalk) {
                    $class = $funWalk->status === 'active' ? 'success' : 'danger';
                    return '<span class="label label-' . $class . '">' . ucfirst($funWalk->status) . '</span>';
                })
                ->addColumn('actions', function ($funWalk) {
                    return '
                        <button class="btn btn-primary btn-sm edit-btn" data-url="' . route('admin.fun-walk.edit', $funWalk->id) . '"> <i class="fa fa-edit"></i> Edit</button>
                        <button class="btn btn-danger btn-sm delete-btn" data-url="' . route('admin.fun-walk.destroy', $funWalk->id) . '"> <i class="fa fa-trash"></i> Delete</button>';
                })
                ->editColumn('date', function ($funWalk) {
                    return $funWalk->date ? $funWalk->date->format('d M Y') : '-';
                })
                ->editColumn('price', function ($funWalk) {
                    return number_format($funWalk->price, 2);
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        }

        return view('admin.fun-walk.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'date'        => 'required|date',
            'location'    => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,inactive',
        ]);

        $funWalk = FunWalk::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Fun Walk created successfully',
            'data'    => $funWalk
        ]);
    }

    public function edit(FunWalk $funWalk)
    {
        return response()->json([
            'success' => true,
            'url'     => route('admin.fun-walk.update', $funWalk->id),
            'data'    => $funWalk
        ]);
    }

    public function update(Request $request, FunWalk $funWalk)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'date'        => 'required|date',
            'location'    => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,inactive',
        ]);

        $funWalk->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Fun Walk updated successfully',
            'data'    => $funWalk->fresh()
        ]);
    }

    public function management()
    {
        return view('admin.fun-walk-management.fun-walk-management');
    }

    public function destroy(FunWalk $funWalk)
    {
        $funWalk->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fun Walk deleted successfully'
        ]);
    }
}