<?php

namespace App\Http\Controllers\Admin\Stationery;

use App\Http\Controllers\Controller;
use App\Models\Component;
use App\Models\ComponentStock;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ComponentStockController extends Controller
{
    /**
     * Display allocation rules for a component
     */
    public function index(Request $request, Component $component)
    {
        if ($request->ajax()) {
            $componentStocks = ComponentStock::where('component_id', $component->id)
                ->with(['stockItem.stockType'])
                ->get();
            
            return DataTables::of($componentStocks)
                ->addColumn('stock_item_id', function($row) {
                    return $row->stock_item_id;
                })
                ->addColumn('stock_item_name', function($row) {
                    return $row->stockItem ? $row->stockItem->name : 'N/A';
                })
                ->addColumn('stock_type_name', function($row) {
                    return $row->stockItem && $row->stockItem->stockType 
                        ? $row->stockItem->stockType->name 
                        : 'N/A';
                })
                ->addColumn('rule_display', function($row) {
                    $labels = [
                        'per_candidate' => '<span class="label label-info">Per Candidate</span>',
                        'per_center' => '<span class="label label-success">Per Center</span>',
                        'fixed' => '<span class="label label-primary">Fixed</span>',
                        'conditional' => '<span class="label label-warning">Conditional</span>'
                    ];
                    return $labels[$row->rule_type] ?? $row->rule_type;
                })
                ->addColumn('base_quantity', function($row) {
                    return $row->base_qty;
                })
                ->addColumn('extras_fixed', function($row) {
                    return $row->extras_fixed ?? 0;
                })
                ->addColumn('extras_percent', function($row) {
                    return $row->extras_percentage ?? 0;
                })
                ->addColumn('extras_per_candidate', function($row) {
                    return $row->extras_per_candidate ?? 0;
                })
                ->addColumn('extras_percent_candidates', function($row) {
                    return 0; // This field doesn't exist in model
                })
                ->addColumn('condition_value', function($row) {
                    return 0; // This field doesn't exist in model
                })
                ->addColumn('formula_summary', function($row) {
                    $summary = "Base: {$row->base_qty} × Multiplier: {$row->multiplier}";
                    
                    $extras = [];
                    if ($row->extras_fixed > 0) $extras[] = "Fixed: +{$row->extras_fixed}";
                    if ($row->extras_per_candidate > 0) $extras[] = "Per Candidate: +{$row->extras_per_candidate}";
                    if ($row->extras_percentage > 0) $extras[] = "Percent: +{$row->extras_percentage}%";
                    
                    if (!empty($extras)) {
                        $summary .= "<br><small>" . implode(", ", $extras) . "</small>";
                    }
                    
                    return $summary;
                })
                ->addColumn('test_calculation', function($row) {
                    // Test with 50 candidates
                    $result = $row->calculateAllocation(50, 0, []);
                    $testQty = $result['quantity'];
                    return '<span class="text-primary"><strong>' . $testQty . '</strong></span> <small>(for 50 candidates)</small>';
                })
                ->addColumn('actions', function($row) use ($component) {
                    return '
                        <button class="btn btn-primary btn-sm edit-rule-btn" 
                            data-id="' . $row->id . '" 
                            data-url="' . route('admin.stationery.component-stock.update', [$component->id, $row->id]) . '" 
                            title="Edit Rule">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm delete-rule-btn" 
                            data-url="' . route('admin.stationery.component-stock.destroy', [$component->id, $row->id]) . '" 
                            title="Delete Rule">
                            <i class="fa fa-trash"></i>
                        </button>';
                })
                ->rawColumns(['rule_display', 'formula_summary', 'test_calculation', 'actions'])
                ->make(true);
        }

        $component->load(['subject', 'componentStocks']);
        $stockItems = StockItem::where('is_active', true)
            ->with('stockType')
            ->orderBy('name')
            ->get();

        return view('admin.stationery.components.stock', compact('component', 'stockItems'));
    }

    /**
     * Store a new allocation rule
     */
    public function store(Request $request, Component $component)
    {
        $validated = $request->validate([
            'stock_item_id' => 'required|exists:stationery_stock_items,id',
            'rule_type' => 'required|in:per_candidate,per_center,fixed,conditional',
            'base_quantity' => 'required|numeric|min:0',
            'multiplier' => 'required|numeric|min:0',
            'extras_fixed' => 'nullable|numeric|min:0',
            'extras_percent' => 'nullable|numeric|min:0|max:100',
            'extras_per_candidate' => 'nullable|numeric|min:0',
        ]);

        // Map field names to model columns
        $data = [
            'component_id' => $component->id,
            'stock_item_id' => $validated['stock_item_id'],
            'rule_type' => $validated['rule_type'],
            'base_qty' => $validated['base_quantity'],
            'multiplier' => $validated['multiplier'],
            'extras_fixed' => $validated['extras_fixed'] ?? 0,
            'extras_percentage' => $validated['extras_percent'] ?? 0,
            'extras_per_candidate' => $validated['extras_per_candidate'] ?? 0,
            'is_active' => true,
        ];

        // Check if rule already exists for this stock item
        $existing = ComponentStock::where('component_id', $component->id)
            ->where('stock_item_id', $validated['stock_item_id'])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'An allocation rule already exists for this stock item. Please edit the existing rule.'
            ], 422);
        }

        $componentStock = ComponentStock::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Allocation rule created successfully',
            'data' => $componentStock->load('stockItem.stockType')
        ]);
    }

    /**
     * Get allocation rule for editing
     */
    public function edit(Component $component, ComponentStock $componentStock)
    {
        return response()->json([
            'success' => true,
            'data' => $componentStock->load('stockItem.stockType')
        ]);
    }

    /**
     * Update an allocation rule
     */
    public function update(Request $request, Component $component, ComponentStock $componentStock)
    {
        $validated = $request->validate([
            'stock_item_id' => 'required|exists:stationery_stock_items,id',
            'rule_type' => 'required|in:per_candidate,per_center,fixed,conditional',
            'base_quantity' => 'required|numeric|min:0',
            'multiplier' => 'required|numeric|min:0',
            'extras_fixed' => 'nullable|numeric|min:0',
            'extras_percent' => 'nullable|numeric|min:0|max:100',
            'extras_per_candidate' => 'nullable|numeric|min:0',
        ]);

        // Map field names to model columns
        $data = [
            'stock_item_id' => $validated['stock_item_id'],
            'rule_type' => $validated['rule_type'],
            'base_qty' => $validated['base_quantity'],
            'multiplier' => $validated['multiplier'],
            'extras_fixed' => $validated['extras_fixed'] ?? 0,
            'extras_percentage' => $validated['extras_percent'] ?? 0,
            'extras_per_candidate' => $validated['extras_per_candidate'] ?? 0,
        ];

        // Check if changing to a stock item that already has a rule
        if ($componentStock->stock_item_id != $validated['stock_item_id']) {
            $existing = ComponentStock::where('component_id', $component->id)
                ->where('stock_item_id', $validated['stock_item_id'])
                ->where('id', '!=', $componentStock->id)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'An allocation rule already exists for this stock item.'
                ]);
            }
        }

        $componentStock->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Allocation rule updated successfully',
            'data' => $componentStock->fresh()->load('stockItem.stockType')
        ]);
    }

    /**
     * Delete an allocation rule
     */
    public function destroy(Component $component, ComponentStock $componentStock)
    {
        $componentStock->delete();

        return response()->json([
            'success' => true,
            'message' => 'Allocation rule deleted successfully'
        ]);
    }

    /**
     * Test calculation with sample data
     */
    public function testCalculation(Request $request, Component $component)
    {
        $validated = $request->validate([
            'stock_item_id' => 'required|exists:stationery_stock_items,id',
            'candidates' => 'required|integer|min:1',
            'centers' => 'nullable|integer|min:1'
        ]);

        $componentStock = ComponentStock::where('component_id', $component->id)
            ->where('stock_item_id', $validated['stock_item_id'])
            ->first();

        if (!$componentStock) {
            return response()->json([
                'success' => false,
                'message' => 'No allocation rule found for this stock item'
            ], 404);
        }

        $result = $componentStock->calculateAllocation(
            $validated['candidates'],
            $validated['centers'] ?? 0,
            []
        );

        return response()->json([
            'success' => true,
            'quantity' => $result['quantity'],
            'breakdown' => $this->getCalculationBreakdown(
                $componentStock, 
                $validated['candidates'], 
                $validated['centers'] ?? 1
            )
        ]);
    }

    /**
     * Get detailed calculation breakdown
     */
    private function getCalculationBreakdown($rule, $candidates, $centers)
    {
        $breakdown = [];
        
        // Step 1: Base calculation
        switch ($rule->rule_type) {
            case 'per_candidate':
                $base = $rule->base_qty * $candidates;
                $breakdown[] = "Base: {$rule->base_qty} × {$candidates} candidates = {$base}";
                break;
            case 'per_center':
                $base = $rule->base_qty * $centers;
                $breakdown[] = "Base: {$rule->base_qty} × {$centers} centers = {$base}";
                break;
            case 'fixed':
                $base = $rule->base_qty;
                $breakdown[] = "Base: Fixed {$base}";
                break;
            case 'conditional':
                $base = $rule->base_qty * $candidates;
                $breakdown[] = "Base: {$rule->base_qty} × {$candidates} candidates = {$base}";
                break;
            default:
                $base = 0;
        }

        // Step 2: Apply multiplier
        $afterMultiplier = $base * $rule->multiplier;
        $breakdown[] = "After Multiplier: {$base} × {$rule->multiplier} = {$afterMultiplier}";

        $total = $afterMultiplier;

        // Step 3: Add fixed extras
        if ($rule->extras_fixed > 0) {
            $total += $rule->extras_fixed;
            $breakdown[] = "Fixed Extras: +{$rule->extras_fixed} = {$total}";
        }

        // Step 4: Add per-candidate extras
        if ($rule->extras_per_candidate > 0) {
            $candidateExtras = $candidates * $rule->extras_per_candidate;
            $total += $candidateExtras;
            $breakdown[] = "Per Candidate Extras: {$candidates} × {$rule->extras_per_candidate} = +{$candidateExtras} = {$total}";
        }

        // Step 5: Add percentage extras
        if ($rule->extras_percentage > 0) {
            $percentExtras = $afterMultiplier * ($rule->extras_percentage / 100);
            $total += $percentExtras;
            $breakdown[] = "Percentage Extras: {$afterMultiplier} × {$rule->extras_percentage}% = +{$percentExtras} = {$total}";
        }

        $breakdown[] = "Final (Rounded Up): " . ceil($total);

        return $breakdown;
    }
}