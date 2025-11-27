<?php

namespace App\Http\Controllers\Admin\Stationery;

use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Models\CenterCandidate;
use App\Models\CenterStock;
use App\Models\Component;
use App\Models\ComponentStock;
use App\Models\Session;
use App\Models\StockItem;
use App\Models\Subject;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class CenterAllocationController extends Controller
{
    /**
     * Display the allocation report page
     */
    public function index()
    {
        $centers = Center::orderBy('center_no')
            ->get(['center_no', 'center_name']);
        
        $sessions = Session::orderBy('session')
            ->get(['id', 'session']);
        
        $subjects = Subject::orderBy('subject_name')
            ->get(['subject_code', 'subject_name']);

        return view('admin.stationery.allocation.index', compact('centers', 'sessions', 'subjects'));
    }

    /**
     * Generate allocation report
     */
    public function generateReport(Request $request)
    {
        $validated = $request->validate([
            'center_no' => 'required|exists:centers,center_no',
            'session_id' => 'required|exists:sessions,id',
            'subject_code' => 'nullable|exists:subjects,subject_code',
        ]);

        $center = Center::where('center_no', $validated['center_no'])->first();
        
        if (!$center) {
            return response()->json([
                'success' => false,
                'message' => 'Center not found'
            ]);
        }

        // Get candidates for this center and session
        $candidatesQuery = CenterCandidate::where('center_no', $validated['center_no'])
            ->where('session', $validated['session_id']);
        
        if (isset($validated['subject_code'])) {
            // Assuming you have a way to filter by subject - adjust as needed
            $candidatesQuery->where('subject_number', 'LIKE', '%' . $validated['subject_code'] . '%');
        }
        
        $numCandidates = $candidatesQuery->count();
        
        if ($numCandidates == 0) {
            return response()->json([
                'success' => false,
                'message' => 'No candidates found for this center and session'
            ]);
        }

        // Get number of invigilators
        $numInvigilators = $this->estimateInvigilators($numCandidates);

        // Get components for the subject(s)
        $components = $this->getComponentsForAllocation($validated);

        $allocations = [];
        
        foreach ($components as $component) {
            // Get component stocks with allocation rules
            $componentStocks = ComponentStock::where('component_id', $component->id)
                ->where('is_active', true)
                ->with(['stockItem.stockType'])
                ->get();

            foreach ($componentStocks as $componentStock) {
                // Calculate allocation
                $calculation = $componentStock->calculateAllocation(
                    $numCandidates,
                    $numInvigilators,
                    []
                );

                $stockItem = $componentStock->stockItem;
                $availableStock = $stockItem->stock_qty ?? 0;
                $canAllocate = $availableStock >= $calculation['quantity'];

                $allocations[] = [
                    'component' => $component,
                    'stock_item' => $stockItem,
                    'component_stock' => $componentStock,
                    'required_qty' => $calculation['quantity'],
                    'available_stock' => $availableStock,
                    'can_allocate' => $canAllocate,
                    'remaining_stock' => $availableStock - $calculation['quantity'],
                    'breakdown' => $calculation['breakdown'],
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'center' => $center,
                'num_candidates' => $numCandidates,
                'num_invigilators' => $numInvigilators,
                'allocations' => $allocations,
                'session' => Session::find($validated['session_id']),
                'subject' => isset($validated['subject_code']) 
                    ? Subject::find($validated['subject_code']) 
                    : null,
            ]
        ]);
    }

    /**
     * Save allocation to database
     */
    public function saveAllocation(Request $request)
    {
        $validated = $request->validate([
            'center_no' => 'required|exists:centers,center_no',
            'session_id' => 'required|exists:sessions,id',
            'allocations' => 'required|array',
            'allocations.*.component_id' => 'required|exists:components,id',
            'allocations.*.stock_item_id' => 'required|exists:stationery_stock_items,id',
            'allocations.*.allocated_qty' => 'required|numeric|min:0',
            'allocations.*.breakdown' => 'nullable|array',
        ]);

        DB::beginTransaction();
        
        try {
            $savedAllocations = [];

            foreach ($validated['allocations'] as $allocationData) {
                // Check if stock is available
                $stockItem = StockItem::findOrFail($allocationData['stock_item_id']);
                
                if (($stockItem->stock_qty ?? 0) < $allocationData['allocated_qty']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for {$stockItem->name}. Available: {$stockItem->stock_qty}, Required: {$allocationData['allocated_qty']}"
                    ]);
                }

                // Create or update allocation
                $allocation = CenterStock::updateOrCreate(
                    [
                        'center_id' => $validated['center_no'],
                        'session_id' => $validated['session_id'],
                        'component_id' => $allocationData['component_id'],
                        'stock_item_id' => $allocationData['stock_item_id'],
                    ],
                    [
                        'quantity_allocated' => $allocationData['allocated_qty'],
                        'allocation_breakdown' => $allocationData['breakdown'] ?? [],
                        'status' => 'allocated',
                    ]
                );

                // Update stock item quantity
                $stockItem->decrement('stock_qty', $allocationData['allocated_qty']);

                $savedAllocations[] = $allocation;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Allocations saved successfully',
                'data' => $savedAllocations
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error saving allocations: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * View saved allocations for a center
     */
    public function viewAllocations(Request $request)
    {
        if ($request->ajax()) {
            $allocations = CenterStock::with([
                'center',
                'stockItem.stockType',
                'component',
                'session'
            ])
            ->when($request->center_no, function($q) use ($request) {
                $q->where('center_id', $request->center_no);
            })
            ->when($request->session_id, function($q) use ($request) {
                $q->where('session_id', $request->session_id);
            })
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderBy('created_at', 'desc');

            return DataTables::of($allocations)
                ->addColumn('center_name', function($row) {
                    return $row->center->center_name ?? '-';
                })
                ->addColumn('stock_item_name', function($row) {
                    return $row->stockItem->name ?? '-';
                })
                ->addColumn('component_name', function($row) {
                    return $row->component->component_name ?? '-';
                })
                ->addColumn('session_name', function($row) {
                    return $row->session->session ?? '-';
                })
                ->addColumn('status_badge', function($row) {
                    $colors = [
                        'pending' => 'warning',
                        'allocated' => 'info',
                        'dispatched' => 'success',
                        'received' => 'primary',
                        'cancelled' => 'danger',
                    ];
                    $color = $colors[$row->status] ?? 'default';
                    return '<span class="label label-'.$color.'">'.ucfirst($row->status).'</span>';
                })
                ->addColumn('actions', function($row) {
                    return '
                        <button class="btn btn-info btn-sm view-breakdown-btn" 
                            data-id="'.$row->id.'" 
                            title="View Breakdown">
                            <i class="fa fa-calculator"></i>
                        </button>
                        <button class="btn btn-success btn-sm dispatch-btn" 
                            data-id="'.$row->id.'" 
                            title="Mark Dispatched"
                            ' . ($row->status !== 'allocated' ? 'disabled' : '') . '>
                            <i class="fa fa-truck"></i>
                        </button>
                        <button class="btn btn-danger btn-sm cancel-btn" 
                            data-id="'.$row->id.'" 
                            title="Cancel Allocation"
                            ' . (in_array($row->status, ['dispatched', 'received']) ? 'disabled' : '') . '>
                            <i class="fa fa-times"></i>
                        </button>';
                })
                ->rawColumns(['status_badge', 'actions'])
                ->make(true);
        }

        $centers = Center::orderBy('center_no')->get(['center_no', 'center_name']);
        $sessions = Session::orderBy('session')->get(['id', 'session']);

        return view('admin.stationery.allocation.view', compact('centers', 'sessions'));
    }

    /**
     * Mark allocation as dispatched
     */
    public function markDispatched(Request $request, $id)
    {
        $allocation = CenterStock::findOrFail($id);
        
        if ($allocation->status !== 'allocated') {
            return response()->json([
                'success' => false,
                'message' => 'Can only dispatch allocated items'
            ]);
        }
        
        $validated = $request->validate([
            'dispatched_qty' => 'nullable|numeric|min:0|max:'.$allocation->quantity_allocated,
        ]);

        $dispatchedQty = $validated['dispatched_qty'] ?? $allocation->quantity_allocated;
        $allocation->markDispatched($dispatchedQty);

        return response()->json([
            'success' => true,
            'message' => 'Allocation marked as dispatched',
            'data' => $allocation->fresh()
        ]);
    }

    /**
     * Cancel allocation and return stock
     */
    public function cancelAllocation($id)
    {
        $allocation = CenterStock::findOrFail($id);
        
        if (in_array($allocation->status, ['dispatched', 'received'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel dispatched or received allocation'
            ]);
        }

        DB::beginTransaction();
        
        try {
            // Return stock to inventory
            $allocation->stockItem->increment('stock_qty', $allocation->quantity_allocated);
            
            $allocation->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Allocation cancelled and stock returned'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling allocation: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get allocation breakdown details
     */
    public function getBreakdown($id)
    {
        $allocation = CenterStock::with(['stockItem', 'component'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'allocation' => $allocation,
                'breakdown' => $allocation->allocation_breakdown,
            ]
        ]);
    }

    /**
     * Helper: Estimate number of invigilators based on candidates
     */
    private function estimateInvigilators($numCandidates)
    {
        // Assuming 1 invigilator per 25 candidates
        return (int) ceil($numCandidates / 25);
    }

    /**
     * Helper: Get components for allocation
     */
    private function getComponentsForAllocation($validated)
    {
        $query = Component::query();

        if (isset($validated['subject_code'])) {
            $query->where('subject_code', $validated['subject_code']);
        }

        return $query->get();
    }
}