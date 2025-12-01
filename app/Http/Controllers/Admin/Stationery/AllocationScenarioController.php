<?php

namespace App\Http\Controllers\Admin\Stationery;

use App\Http\Controllers\Controller;
use App\Models\AllocationScenario;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AllocationScenarioController extends Controller
{
    /**
     * Display listing of scenarios
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $scenarios = AllocationScenario::query();

            if ($request->condition_type) {
                $scenarios->where('condition_type', $request->condition_type);
            }

            if ($request->has('is_active')) {
                $scenarios->where('is_active', $request->is_active);
            }

            return DataTables::of($scenarios)
                ->addColumn('condition_details', function($row) {
                    $details = '';
                    
                    switch($row->condition_type) {
                        case 'candidate_range':
                            $min = $row->condition_min ?? '0';
                            $max = $row->condition_max ?? '∞';
                            $details = "Candidates: {$min} - {$max}";
                            break;
                        
                        case 'component_type':
                        case 'center_location':
                        case 'custom':
                            $attr = $row->condition_attribute ?? 'N/A';
                            $val = $row->condition_value ?? 'N/A';
                            $details = "{$attr} = {$val}";
                            break;
                    }
                    
                    return '<code>' . $details . '</code>';
                })
                ->addColumn('formula_parts', function($row) {
                    $parts = [];
                    if ($row->use_multiplier) $parts[] = 'Multiplier';
                    if ($row->use_fixed_extras) $parts[] = 'Fixed';
                    if ($row->use_per_candidate_extras) $parts[] = 'Per-Candidate';
                    if ($row->use_percentage_extras) $parts[] = 'Percentage';
                    
                    if (empty($parts)) {
                        return '<span class="label label-default">Base Only</span>';
                    }
                    
                    return '<small>' . implode(', ', $parts) . '</small>';
                })
                ->addColumn('status_badge', function($row) {
                    $class = $row->is_active ? 'success' : 'danger';
                    $text = $row->is_active ? 'Active' : 'Inactive';
                    return '<span class="label label-'.$class.'">'.$text.'</span>';
                })
                ->addColumn('actions', function($row) {
                    return '
                        <button class="btn btn-info btn-sm view-scenario-btn" 
                            data-url="'.route('admin.stationery.scenarios.show', $row->id).'" 
                            title="View">
                            <i class="fa fa-eye"></i>
                        </button>
                        <button class="btn btn-primary btn-sm edit-scenario-btn" 
                            data-url="'.route('admin.stationery.scenarios.edit', $row->id).'" 
                            title="Edit">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm delete-scenario-btn" 
                            data-url="'.route('admin.stationery.scenarios.destroy', $row->id).'" 
                            title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>';
                })
                ->rawColumns(['condition_details', 'formula_parts', 'status_badge', 'actions'])
                ->make(true);
        }

        return view('admin.stationery.scenarios.index');
    }

    /**
     * Store new scenario
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:allocation_scenarios,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'condition_type' => 'required|in:candidate_range,component_type,center_location,custom',
            'condition_min' => 'nullable|integer|min:0',
            'condition_max' => 'nullable|integer|min:0',
            'condition_attribute' => 'nullable|string|max:255',
            'condition_value' => 'nullable|string|max:255',
            'use_multiplier' => 'nullable|boolean',
            'use_fixed_extras' => 'nullable|boolean',
            'use_per_candidate_extras' => 'nullable|boolean',
            'use_percentage_extras' => 'nullable|boolean',
            'priority' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        // Convert checkboxes to boolean
        $validated['use_multiplier'] = $request->has('use_multiplier');
        $validated['use_fixed_extras'] = $request->has('use_fixed_extras');
        $validated['use_per_candidate_extras'] = $request->has('use_per_candidate_extras');
        $validated['use_percentage_extras'] = $request->has('use_percentage_extras');
        $validated['is_active'] = $request->has('is_active');

        $scenario = AllocationScenario::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Scenario created successfully',
            'data' => $scenario
        ]);
    }

    /**
     * Show scenario details
     */
    public function show(AllocationScenario $scenario)
    {
        $scenario->load('componentStocks.component', 'componentStocks.stockItem');
        
        return response()->json([
            'success' => true,
            'data' => $scenario
        ]);
    }

    /**
     * Get scenario for editing
     */
    public function edit(AllocationScenario $scenario)
    {
        return response()->json([
            'success' => true,
            'url' => route('admin.stationery.scenarios.update', $scenario->id),
            'data' => $scenario
        ]);
    }

    /**
     * Update scenario
     */
    public function update(Request $request, AllocationScenario $scenario)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:allocation_scenarios,code,' . $scenario->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'condition_type' => 'required|in:candidate_range,component_type,center_location,custom',
            'condition_min' => 'nullable|integer|min:0',
            'condition_max' => 'nullable|integer|min:0',
            'condition_attribute' => 'nullable|string|max:255',
            'condition_value' => 'nullable|string|max:255',
            'use_multiplier' => 'nullable|boolean',
            'use_fixed_extras' => 'nullable|boolean',
            'use_per_candidate_extras' => 'nullable|boolean',
            'use_percentage_extras' => 'nullable|boolean',
            'priority' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        // Convert checkboxes to boolean
        $validated['use_multiplier'] = $request->has('use_multiplier');
        $validated['use_fixed_extras'] = $request->has('use_fixed_extras');
        $validated['use_per_candidate_extras'] = $request->has('use_per_candidate_extras');
        $validated['use_percentage_extras'] = $request->has('use_percentage_extras');
        $validated['is_active'] = $request->has('is_active');

        $scenario->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Scenario updated successfully',
            'data' => $scenario->fresh()
        ]);
    }

    /**
     * Delete scenario
     */
    public function destroy(AllocationScenario $scenario)
    {
        // Check if scenario is linked to any component stocks
        if ($scenario->componentStocks()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete scenario that is linked to allocation rules'
            ], 400);
        }

        $scenario->delete();

        return response()->json([
            'success' => true,
            'message' => 'Scenario deleted successfully'
        ]);
    }

    /**
     * Get scenarios for dropdown/selection
     */
    public function getOptions()
    {
        $scenarios = AllocationScenario::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'condition_type']);

        return response()->json([
            'success' => true,
            'data' => $scenarios
        ]);
    }

    /**
     * Test scenario matching
     */
    public function testMatch(Request $request)
    {
        $validated = $request->validate([
            'scenario_id' => 'required|exists:allocation_scenarios,id',
            'num_candidates' => 'required|integer|min:0',
            'attributes' => 'nullable|array',
        ]);

        $scenario = AllocationScenario::findOrFail($validated['scenario_id']);
        $matches = $scenario->matches(
            $validated['num_candidates'],
            $validated['attributes'] ?? []
        );

        return response()->json([
            'success' => true,
            'data' => [
                'scenario' => $scenario,
                'matches' => $matches,
                'input' => [
                    'num_candidates' => $validated['num_candidates'],
                    'attributes' => $validated['attributes'] ?? []
                ]
            ]
        ]);
    }

    /**
     * Bulk activate/deactivate scenarios
     */
    public function bulkStatus(Request $request)
    {
        $validated = $request->validate([
            'scenario_ids' => 'required|array',
            'scenario_ids.*' => 'exists:allocation_scenarios,id',
            'status' => 'required|boolean',
        ]);

        AllocationScenario::whereIn('id', $validated['scenario_ids'])
            ->update(['is_active' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Scenarios updated successfully'
        ]);
    }
}