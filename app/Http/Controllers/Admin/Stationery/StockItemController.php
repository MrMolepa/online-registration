<?php

namespace App\Http\Controllers\Admin\Stationery;

use App\Http\Controllers\Controller;
use App\Models\StockItem;
use App\Models\StockType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StockItemController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $stockItems = StockItem::with('stockType')
                ->select(['id', 'stock_type_id', 'name', 'unit', 'stock_qty', 'is_active', 'created_at']);
            
            return DataTables::of($stockItems)
                ->addColumn('stock_type_name', function($stockItem) {
                    return $stockItem->stockType ? $stockItem->stockType->name : '-';
                })
                ->addColumn('status_badge', function($stockItem) {
                    $class = $stockItem->is_active ? 'success' : 'danger';
                    $text = $stockItem->is_active ? 'Active' : 'Inactive';
                    return '<span class="label label-'.$class.'">'.$text.'</span>';
                })
                ->addColumn('stock_display', function($stockItem) {
                    if ($stockItem->stock_qty <= 0) {
                        return '<span class="label label-danger">Out of Stock</span>';
                    } elseif ($stockItem->stock_qty < 50) {
                        return '<span class="label label-warning">Low Stock (' . number_format($stockItem->stock_qty, 0) . ' ' . $stockItem->unit . ')</span>';
                    } else {
                        return '<span class="label label-success">' . number_format($stockItem->stock_qty, 0) . ' ' . $stockItem->unit . '</span>';
                    }
                })
                ->editColumn('created_at', function($stockItem) {
                    return $stockItem->created_at ? $stockItem->created_at->format('Y-m-d H:i') : '-';
                })
                ->addColumn('actions', function($stockItem) {
                    return '
                        <button class="btn btn-info btn-sm view-btn" data-url="' . route('admin.stationery.stock-items.show', $stockItem->id) . '" title="View">
                            <i class="fa fa-eye"></i>
                        </button>
                        <button class="btn btn-primary btn-sm edit-btn" data-url="' . route('admin.stationery.stock-items.edit', $stockItem->id) . '" title="Edit">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm delete-btn" data-url="' . route('admin.stationery.stock-items.destroy', $stockItem->id) . '" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>';
                })
                ->rawColumns(['status_badge', 'stock_display', 'actions'])
                ->make(true);
        }

        return view('admin.stationery.stock-items.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'stock_type_id' => 'required|exists:stationery_stock_types,id',
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'stock_qty' => 'required|numeric|min:0',
            'supplier_info' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $stockItem = StockItem::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Stock item created successfully',
            'data' => $stockItem->load('stockType')
        ]);
    }

    public function show(StockItem $stockItem)
    {
        $stockItem->load(['stockType', 'componentStocks.component', 'centerStocks.center']);
        
        return response()->json([
            'success' => true,
            'data' => $stockItem
        ]);
    }

    public function edit(StockItem $stockItem)
    {
        return response()->json([
            'success' => true,
            'url' => route('admin.stationery.stock-items.update', $stockItem->id),
            'data' => $stockItem
        ]);
    }

    public function update(Request $request, StockItem $stockItem)
    {
        $validated = $request->validate([
            'stock_type_id' => 'required|exists:stationery_stock_types,id',
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'stock_qty' => 'required|numeric|min:0',
            'supplier_info' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $stockItem->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Stock item updated successfully',
            'data' => $stockItem->fresh()->load('stockType')
        ]);
    }

    public function destroy(StockItem $stockItem)
    {
        try {
            // Check if stock item is linked to components
            $rulesCount = $stockItem->componentStocks()->count();
            
            if ($rulesCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete stock item "' . $stockItem->name . '". It is linked to ' . $rulesCount . ' allocation rule(s). Please delete the rules first.'
                ], 422);
            }

            // Check if stock item has center allocations
            $allocationsCount = $stockItem->centerStocks()->count();
            
            if ($allocationsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete stock item "' . $stockItem->name . '". It has ' . $allocationsCount . ' center allocation(s).'
                ], 422);
            }

            // Store name for success message
            $name = $stockItem->name;
            
            // Delete the stock item
            $stockItem->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Stock item "' . $name . '" deleted successfully'
            ]);
            
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error deleting stock item: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete stock item due to database constraints. It may be in use.'
            ], 500);
            
        } catch (\Exception $e) {
            \Log::error('Error deleting stock item: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting stock item: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get stock items for dropdowns
    public function getOptions(Request $request)
    {
        $query = StockItem::where('is_active', true)->with('stockType');
        
        // Filter by stock type if provided
        if ($request->has('stock_type_id') && $request->stock_type_id) {
            $query->where('stock_type_id', $request->stock_type_id);
        }
        
        $stockItems = $query->orderBy('name')->get(['id', 'name', 'unit', 'stock_type_id']);

        return response()->json([
            'success' => true,
            'data' => $stockItems
        ]);
    }

    // Adjust stock quantity
    public function adjustStock(Request $request, StockItem $stockItem)
    {
        $validated = $request->validate([
            'adjustment_type' => 'required|in:add,subtract,set',
            'quantity' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $oldQty = $stockItem->stock_qty;

        switch ($validated['adjustment_type']) {
            case 'add':
                $stockItem->stock_qty += $validated['quantity'];
                break;
            case 'subtract':
                $stockItem->stock_qty -= $validated['quantity'];
                if ($stockItem->stock_qty < 0) {
                    $stockItem->stock_qty = 0;
                }
                break;
            case 'set':
                $stockItem->stock_qty = $validated['quantity'];
                break;
        }

        $stockItem->save();

        return response()->json([ 
            'success' => true,
            'message' => 'Stock quantity adjusted successfully',
            'old_qty' => $oldQty,
            'new_qty' => $stockItem->stock_qty,
            'data' => $stockItem
        ]);
    }
}


