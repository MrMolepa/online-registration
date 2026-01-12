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
     * Display allocation rules management page
     */
    public function index(Request $request)
    {
        // Handle DataTables AJAX request
        if ($request->ajax() && ($request->has('component_key') || $request->has('component_id'))) {
            $inputComponent = $request->component_key ?? $request->component_id;
            // Resolve numeric id to padded key if necessary
            if (is_numeric($inputComponent)) {
                $comp = Component::find($inputComponent);
                if ($comp) {
                    $inputComponent = str_pad($comp->subject_code, 4, '0', STR_PAD_LEFT) . '-' . str_pad($comp->component_code, 2, '0', STR_PAD_LEFT);
                }
            }

            $componentStocks = ComponentStock::where('component_key', $inputComponent)
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
                    $result = $row->calculateAllocation(50, 0, []);
                    $testQty = $result['quantity'];
                    return '<span class="text-primary"><strong>' . $testQty . '</strong></span> <small>(for 50 candidates)</small>';
                })
                ->addColumn('actions', function($row) {
                    return '
                        <button class="btn btn-primary btn-sm edit-rule-btn" 
                            data-id="' . $row->id . '" 
                            title="Edit Rule">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm delete-rule-btn" 
                            data-id="' . $row->id . '" 
                            title="Delete Rule">
                            <i class="fa fa-trash"></i>
                        </button>';
                })
                ->rawColumns(['rule_display', 'formula_summary', 'test_calculation', 'actions'])
                ->make(true);
        }

        // Load initial data for the page
        $components = Component::with('subject')
            ->orderBy('component_code')
            ->get();
            
        $stockItems = StockItem::where('is_active', true)
            ->with('stockType')
            ->orderBy('name')
            ->get();

        return view('admin.stationery.stock', compact('components', 'stockItems'));
    }

    /**
     * Get component details
     */
    public function getComponent(Request $request)
    {
        $validated = $request->validate([
            'component_key' => 'nullable|string',
            'component_id' => 'nullable|integer'
        ]);

        $component = null;
        if (!empty($validated['component_key'])) {
            // parse key like 0001-01
            $parts = explode('-', $validated['component_key']);
            if (count($parts) >= 2) {
                $subject = ltrim($parts[0], '0');
                $compCode = ltrim($parts[1], '0');
                $component = Component::with('subject')
                    ->where('subject_code', $subject)
                    ->where('component_code', $compCode)
                    ->first();
            }
        } elseif (!empty($validated['component_id'])) {
            $component = Component::with('subject')->find($validated['component_id']);
        }

        if (!$component) {
            return response()->json(['success' => false, 'message' => 'Component not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $component
        ]);
    }

    /**
     * Store a new allocation rule
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'component_key' => 'required|string',
            'stock_item_id' => 'required|exists:stationery_stock_items,id',
            'rule_type' => 'required|in:per_candidate,per_center,fixed,conditional',
            'base_quantity' => 'required|numeric|min:0',
            'multiplier' => 'required|numeric|min:0',
            'extras_fixed' => 'nullable|numeric|min:0',
            'extras_percent' => 'nullable|numeric|min:0|max:100',
            'extras_per_candidate' => 'nullable|numeric|min:0',
        ]);
        // resolve provided key to a canonical padded key and ensure component exists
        $parts = explode('-', $validated['component_key']);
        if (count($parts) < 2) {
            return response()->json(['success' => false, 'message' => 'Invalid component key'], 422);
        }
        $subject = ltrim($parts[0], '0');
        $compCode = ltrim($parts[1], '0');
        $comp = Component::where('subject_code', $subject)->where('component_code', $compCode)->first();
        if (!$comp) {
            return response()->json(['success' => false, 'message' => 'Component not found'], 422);
        }

        $canonicalKey = str_pad($comp->subject_code, 4, '0', STR_PAD_LEFT) . '-' . str_pad($comp->component_code, 2, '0', STR_PAD_LEFT);

        $data = [
            'component_key' => $canonicalKey,
            'component_id' => $comp->id,
            'stock_item_id' => $validated['stock_item_id'],
            'rule_type' => $validated['rule_type'],
            'base_qty' => $validated['base_quantity'],
            'multiplier' => $validated['multiplier'],
            'extras_fixed' => $validated['extras_fixed'] ?? 0,
            'extras_percentage' => $validated['extras_percent'] ?? 0,
            'extras_per_candidate' => $validated['extras_per_candidate'] ?? 0,
            'is_active' => true,
        ];

        // Check if rule already exists
        $existing = ComponentStock::where('component_key', $canonicalKey)
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
    public function edit(ComponentStock $componentStock)
    {
        $componentStock->load('stockItem.stockType', 'component');
        $component = $componentStock->component ?? $componentStock->resolveComponentFromKey();
        // ensure we return a component_id field for the frontend but store the key
        $componentStock->component_id = $componentStock->component_key;
        return response()->json([
            'success' => true,
            'data' => $componentStock,
            'component' => $component
        ]);
    }

    /**
     * Update an allocation rule
     */
    public function update(Request $request, ComponentStock $componentStock)
    {
        $validated = $request->validate([
            'component_key' => 'required|string',
            'stock_item_id' => 'required|exists:stationery_stock_items,id',
            'rule_type' => 'required|in:per_candidate,per_center,fixed,conditional',
            'base_quantity' => 'required|numeric|min:0',
            'multiplier' => 'required|numeric|min:0',
            'extras_fixed' => 'nullable|numeric|min:0',
            'extras_percent' => 'nullable|numeric|min:0|max:100',
            'extras_per_candidate' => 'nullable|numeric|min:0',
        ]);
        $parts = explode('-', $validated['component_key']);
        if (count($parts) < 2) {
            return response()->json(['success' => false, 'message' => 'Invalid component key'], 422);
        }
        $subject = ltrim($parts[0], '0');
        $compCode = ltrim($parts[1], '0');
        $comp = Component::where('subject_code', $subject)->where('component_code', $compCode)->first();
        if (!$comp) {
            return response()->json(['success' => false, 'message' => 'Component not found'], 422);
        }
        $canonicalKey = str_pad($comp->subject_code, 4, '0', STR_PAD_LEFT) . '-' . str_pad($comp->component_code, 2, '0', STR_PAD_LEFT);

        $data = [
            'component_key' => $canonicalKey,
            'component_id' => $comp->id,
            'stock_item_id' => $validated['stock_item_id'],
            'rule_type' => $validated['rule_type'],
            'base_qty' => $validated['base_quantity'],
            'multiplier' => $validated['multiplier'],
            'extras_fixed' => $validated['extras_fixed'] ?? 0,
            'extras_percentage' => $validated['extras_percent'] ?? 0,
            'extras_per_candidate' => $validated['extras_per_candidate'] ?? 0,
        ];

        // Check for duplicate rules
        if ($componentStock->stock_item_id != $validated['stock_item_id'] || 
            $componentStock->component_key != $canonicalKey) {
            $existing = ComponentStock::where('component_key', $canonicalKey)
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

        $fresh = $componentStock->fresh()->load('stockItem.stockType', 'component');
        $component = $componentStock->component ?? $componentStock->resolveComponentFromKey();

        return response()->json([
            'success' => true,
            'message' => 'Allocation rule updated successfully',
            'data' => $fresh,
            'component' => $component
        ]);
    }

    /**
     * Delete an allocation rule
     */
    public function destroy(ComponentStock $componentStock)
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
    public function testCalculation(Request $request)
    {

        $validated = $request->validate([
            'component_key' => 'required|string',
            'stock_item_id' => 'required|exists:stationery_stock_items,id',
            'candidates' => 'required|integer|min:1',
            'centers' => 'nullable|integer|min:1'
        ]);

        $parts = explode('-', $validated['component_key']);
        if (count($parts) < 2) {
            return response()->json(['success' => false, 'message' => 'Invalid component key'], 422);
        }

        $subject = ltrim($parts[0], '0');
        $compCode = ltrim($parts[1], '0');
        $comp = Component::where('subject_code', $subject)->where('component_code', $compCode)->first();
        if (!$comp) {
            return response()->json(['success' => false, 'message' => 'Component not found'], 422);
        }

        $canonicalKey = str_pad($comp->subject_code, 4, '0', STR_PAD_LEFT) . '-' . str_pad($comp->component_code, 2, '0', STR_PAD_LEFT);

        $componentStock = ComponentStock::where('component_key', $canonicalKey)
            ->where('stock_item_id', $validated['stock_item_id'])
            ->first();

        if (!$componentStock) {
            return response()->json([
                'success' => false,
                'message' => 'No allocation rule found for this stock item and component'
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