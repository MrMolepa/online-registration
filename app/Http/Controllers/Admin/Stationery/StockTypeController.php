<?php

namespace App\Http\Controllers\Admin\Stationery;

use App\Http\Controllers\Controller;
use App\Models\StockType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class StockTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $stockTypes = StockType::withCount('stockItems')
                ->select(['id', 'name', 'description', 'is_active', 'created_at']);
            
            return DataTables::of($stockTypes)
                ->addColumn('status_badge', function($stockType) {
                    $class = $stockType->is_active ? 'success' : 'danger';
                    $text = $stockType->is_active ? 'Active' : 'Inactive';
                    return '<span class="label label-'.$class.'">'.$text.'</span>';
                })
                ->addColumn('items_count', function($stockType) {
                    return $stockType->stock_items_count . ' items'; // Display count of related stock items
                })
                ->addColumn('actions', function($stockType) {
                    return '
                        <button class="btn btn-info btn-sm view-btn" data-url="' . route('admin.stationery.stock-types.show', $stockType->id) . '" title="View">
                            <i class="fa fa-eye"></i>
                        </button>
                        <button class="btn btn-primary btn-sm edit-btn" data-url="' . route('admin.stationery.stock-types.edit', $stockType->id) . '" title="Edit">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm delete-btn" data-url="' . route('admin.stationery.stock-types.destroy', $stockType->id) . '" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>';
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        }

        return view('admin.stationery.stock-types.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $stockType = StockType::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Stock type created successfully',
            'data' => $stockType
        ]);
    }

    public function show(StockType $stockType)
    {
        $stockType->load('stockItems');
        
        return response()->json([
            'success' => true,
            'data' => $stockType // Include related stock items 
        ]);
    }

    public function edit(StockType $stockType)
    {
        return response()->json([
            'success' => true,
            'url' => route('admin.stationery.stock-types.update', $stockType->id),
            'data' => $stockType
        ]);
    }

    public function update(Request $request, StockType $stockType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $stockType->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Stock type updated successfully',
            'data' => $stockType->fresh() // Return the updated model
        ]);
    }

    public function destroy(StockType $stockType)
    {
        // Check if stock type has items
        if ($stockType->stockItems()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete stock type with existing stock items' 
            ],);
        }

        $stockType->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Stock type deleted successfully'
        ]);
    }

    // Get stock types for dropdowns
    public function getOptions()
    {
        $stockTypes = StockType::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $stockTypes
        ]);
    }
}