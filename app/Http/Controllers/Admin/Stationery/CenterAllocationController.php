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
use App\Models\Level;
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
        // Get all centers - will be filtered by level via JavaScript
        $centers = Center::orderBy('center_no')->get(['center_no', 'center_name', 'level']);
        
        $levels = Level::where('is_active', true)
            ->orderBy('id')
            ->get();
        
        // Get all sessions
        $sessions = Session::orderBy('session')->get();
        
        // Load components with subject for dropdown
        $components = Component::with('subject')
            ->orderBy('subject_code')
            ->orderBy('component_code')
            ->get(['id', 'component_name', 'subject_code', 'component_code']);

        $subjects = Subject::orderBy('subject_name')->get(['subject_code', 'subject_name']);

        return view('admin.stationery.allocation.index', compact('centers', 'sessions', 'subjects', 'components', 'levels'));
    }

    /**
     * Get centers filtered by level - AJAX endpoint
     */
    public function getCentersByLevel(Request $request)
    {
        $levelId = $request->level;
        
        \Log::info('Getting centers by level', [
            'level_id_requested' => $levelId
        ]);
        
        // Get the level details to understand what we're filtering by
        $level = Level::find($levelId);
        
        if (!$level) {
            return response()->json([
                'success' => false,
                'message' => 'Level not found'
            ]);
        }
        
        \Log::info('Level found', [
            'level_id' => $level->id,
            'level_description' => $level->description
        ]);
        
        // Try multiple approaches to find centers
        // Approach 1: Check center's level field (might be storing level ID, description, or code)
        $centersDirectMatch = Center::where(function($q) use ($level, $levelId) {
            $q->where('level', $levelId)
              ->orWhere('level', $level->id)
              ->orWhere('level', $level->description);
        })->get(['center_no', 'center_name', 'level']);
        
        // Approach 2: Check many-to-many relationship
        $centersRelationship = Center::whereHas('levels', function($q) use ($levelId) {
            $q->where('levels.id', $levelId);
        })->get(['center_no', 'center_name', 'level']);
        
        // Merge both results and remove duplicates
        $centers = $centersDirectMatch->merge($centersRelationship)->unique('center_no');
        
        \Log::info('Centers found', [
            'direct_match_count' => $centersDirectMatch->count(),
            'relationship_match_count' => $centersRelationship->count(),
            'total_unique_centers' => $centers->count(),
            'sample_center_level_values' => $centers->take(5)->pluck('level')->toArray()
        ]);
        
        // Sort by center_no
        $centers = $centers->sortBy('center_no')->values();
        
        return response()->json([
            'success' => true,
            'centers' => $centers,
            'debug' => [
                'level_searched' => $level->description,
                'direct_matches' => $centersDirectMatch->count(),
                'relationship_matches' => $centersRelationship->count()
            ]
        ]);
    }

    /**
     * Get sessions filtered by financial year - AJAX endpoint
     */
    public function getSessionsByLevel(Request $request)
    {
        $level = $request->level;
        $financialYear = $request->financial_year;
        
        // Get all sessions, optionally filtered by financial year
        $sessions = Session::query()
            ->when($financialYear, function($q) use ($financialYear) {
                $q->where('financial_year', $financialYear);
            })
            ->where('is_active', true)
            ->orderBy('session')
            ->get();
        
        return response()->json([
            'success' => true,
            'sessions' => $sessions
        ]);
    }

    /**
     * Get components filtered by level and session - AJAX endpoint
     */
    public function getComponentsByFilters(Request $request)
    {
        $level = $request->level;
        $sessionId = $request->session_id;
        
        // Get session details if provided
        $session = null;
        if ($sessionId) {
            $session = Session::find($sessionId);
        }
        
        // Build query for components based on subjects that match the level
        $componentsQuery = Component::with('subject')
            ->whereHas('subject', function($q) use ($level) {
                if ($level) {
                    $q->where('level', $level);
                }
            })
            ->orderBy('subject_code')
            ->orderBy('component_code');
        
        $components = $componentsQuery->get(['id', 'component_name', 'subject_code', 'component_code']);
        
        return response()->json([
            'success' => true,
            'components' => $components,
            'session' => $session
        ]);
    }

    /**
     * Generate allocation report
     */
  public function generateReport(Request $request)
    {
        try {
            $validated = $request->validate([
                'center_no' => 'required|exists:centers,center_no',
                'session_id' => 'required|exists:sessions,id',
                //'subject_code' => 'nullable|exists:subjects,subject_code',
            ]);

            \Log::info('Generating allocation report', $validated);

            $center = Center::where('center_no', $validated['center_no'])->first();
            
            if (!$center) {
                return response()->json([
                    'success' => false,
                    'message' => 'Center not found'
                ], 404);
            }

            // Get the session details
            $session = Session::find($validated['session_id']);
            
            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found'
                ], 404);
            }
            
            \Log::info('Session details', [
                'session_id' => $session->id,
                'session_name' => $session->session,
                'financial_year' => $session->financial_year ?? null
            ]);

            // Get candidates for this center and session
            // The center_candidate table has 'session' field which contains the session NAME not ID
            $candidatesQuery = CenterCandidate::where('center_no', $validated['center_no'])
                ->where('session', $session->session); // Match with session name/value
            
            if (isset($validated['subject_code'])) {
                // Filter by subject if provided
                $candidatesQuery->where('subject_number', 'LIKE', '%' . $validated['subject_code'] . '%');
            }
            
            $candidates = $candidatesQuery->get();
            $numCandidates = $candidates->count();
            
            \Log::info('Candidates search result', [
                'center_no' => $validated['center_no'],
                'session_searched' => $session->session,
                'candidates_found' => $numCandidates,
                'total_candidates_in_center' => CenterCandidate::where('center_no', $validated['center_no'])->count(),
                'sample_candidate_session' => $candidates->first() ? $candidates->first()->session : 'no candidates'
            ]);
            
            if ($numCandidates == 0) {
                // Provide helpful debugging info
                $allCandidatesInCenter = CenterCandidate::where('center_no', $validated['center_no'])
                    ->select('session')
                    ->groupBy('session')
                    ->pluck('session')
                    ->toArray();
                
                return response()->json([
                    'success' => false,
                    'message' => 'No candidates found for this center and session. Please verify that candidates are registered.',
                    'debug' => [
                        'searched_for_session' => $session->session,
                        'available_sessions_in_center' => $allCandidatesInCenter,
                        'hint' => 'The session value might not match. Available sessions in this center: ' . implode(', ', $allCandidatesInCenter)
                    ]
                ]);
            }

            // Get number of invigilators
            $numInvigilators = $this->estimateInvigilators($numCandidates);

            // Get components for the subject(s)
            $components = $this->getComponentsForAllocation($validated);
            
            \Log::info('Components found', ['count' => $components->count()]);
            
            if ($components->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No components found for the selected subject(s). Please configure components first.'
                ], 404);
            }

            $allocations = [];
            
            foreach ($components as $component) {
                // Get component stocks with allocation rules
                $componentStocks = ComponentStock::where('component_id', $component->id)
                    ->where('is_active', true)
                    ->with(['stockItem.stockType'])
                    ->get();

                \Log::info('Component stocks found', [
                    'component_id' => $component->id,
                    'component_name' => $component->component_name,
                    'stocks_count' => $componentStocks->count()
                ]);

                if ($componentStocks->isEmpty()) {
                    \Log::warning('No allocation rules found for component', ['component_id' => $component->id]);
                    continue;
                }

                foreach ($componentStocks as $componentStock) {
                    try {
                        // Calculate allocation
                        $calculation = $componentStock->calculateAllocation(
                            $numCandidates,
                            $numInvigilators,
                            []
                        );

                        $stockItem = $componentStock->stockItem;
                        
                        if (!$stockItem) {
                            \Log::warning('Stock item not found', ['component_stock_id' => $componentStock->id]);
                            continue;
                        }
                        
                        $availableStock = $stockItem->stock_qty ?? 0;
                        $canAllocate = $availableStock >= $calculation['quantity'];

                        $allocations[] = [
                            'component' => [
                                'id' => $component->id,
                                'component_code' => $component->component_code,
                                'component_name' => $component->component_name,
                            ],
                            'stock_item' => [
                                'id' => $stockItem->id,
                                'name' => $stockItem->name,
                                'unit' => $stockItem->unit,
                                'stock_type' => $stockItem->stockType ? $stockItem->stockType->name : null,
                            ],
                            'component_stock' => [
                                'id' => $componentStock->id,
                                'rule_type' => $componentStock->rule_type,
                                'base_qty' => $componentStock->base_qty,
                                'multiplier' => $componentStock->multiplier,
                            ],
                            'required_qty' => $calculation['quantity'],
                            'available_stock' => $availableStock,
                            'can_allocate' => $canAllocate,
                            'remaining_stock' => $availableStock - $calculation['quantity'],
                            'breakdown' => $calculation['breakdown'],
                        ];
                    } catch (\Exception $e) {
                        \Log::error('Error calculating allocation', [
                            'component_stock_id' => $componentStock->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
            
            \Log::info('Total allocations generated', ['count' => count($allocations)]);

            return response()->json([
                'success' => true,
                'data' => [
                    'center' => [
                        'center_no' => $center->center_no,
                        'center_name' => $center->center_name,
                        'district' => $center->district,
                    ],
                    'num_candidates' => $numCandidates,
                    'num_invigilators' => $numInvigilators,
                    'allocations' => $allocations,
                    'session' => [
                        'id' => $session->id,
                        'session' => $session->session,
                        'description' => $session->description ?? null,
                    ],
                    'subject' => isset($validated['subject_code']) 
                        ? Subject::find($validated['subject_code']) 
                        : null,
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error generating allocation report', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error generating report: ' . $e->getMessage()
            ]);
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
                'component.subject',
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
                    if ($row->component) {
                        $fullCode = $row->component->subject_code . '-' . $row->component->component_code;
                        return $row->component->component_name . ' (' . $fullCode . ')';
                    }
                    return '-';
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
        $allocation = CenterStock::with(['stockItem', 'component.subject'])
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

        if (isset($validated['component_id'])) {
            $query->where('id', $validated['component_id']);
        } elseif (isset($validated['subject_code'])) {
            $query->where('subject_code', $validated['subject_code']);
        }

        return $query->get();
    }
}