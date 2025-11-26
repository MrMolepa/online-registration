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
                ->select(['id', 'stock_type_id', 'name', 'unit', 'stock_qty','is_active', 'created_at']);
            
            return DataTables::of($stockItems)
                ->addColumn('stock_type_name', function($stockItem) {
                    return $stockItem->stockType ? $stockItem->stockType->name : '-';
                })
                ->addColumn('status_badge', function($stockItem) {
                    $class = $stockItem->is_active ? 'success' : 'danger';
                    $text = $stockItem->is_active ? 'Active' : 'Inactive';
                    return '<span class="label label-'.$class.'">'.$text.'</span>';
                })
                ->addColumn('stock_status', function($stockItem) {
                    if ($stockItem->stock_qty <= 0) {
                        return '<span class="label label-danger">Out of Stock</span>';
                    } elseif ($stockItem->stock_qty < 50) {
                        return '<span class="label label-warning">Low Stock</span>';
                    } else {
                        return '<span class="label label-success">In Stock</span>';
                    }
                })
                ->editColumn('stock_qty', function($stockItem) {
                    return number_format($stockItem->stock_qty, 0) . ' ' . $stockItem->unit;
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
                ->rawColumns(['status_badge', 'stock_status', 'actions'])
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
        $stockItem->load('stockType', 'componentStocks.component', 'centerStocks.center');
        
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
            'data' => $stockItem->fresh('stockType')
        ]);
    }

    public function destroy(StockItem $stockItem)
    {
        // Check if stock item is used in allocations
        if ($stockItem->componentStocks()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete stock item that is linked to components'
            ],);
        }

        if ($stockItem->centerStocks()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete stock item with existing allocations'
            ],);
        }

        $stockItem->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Stock item deleted successfully'
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

        $oldQty = $stockItem->stock_qty;// store old quantity for reference

        switch ($validated['adjustment_type']) {
            case 'add':
                $stockItem->stock_qty += $validated['quantity'];// this adds the quantity
                break;
            case 'subtract':
                $stockItem->stock_qty -= $validated['quantity'];// this subtracts the quantity
                if ($stockItem->stock_qty < 0) {
                    $stockItem->stock_qty = 0;// prevent negative stock
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