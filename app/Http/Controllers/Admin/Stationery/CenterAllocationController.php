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
use Illuminate\Support\Facades\Session as FacadesSession;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class CenterAllocationController extends Controller
{
    /**
     * Display the allocation report page
     */
    public function index()
    {
        $levels = Level::where('is_active', true)->orderBy('id')->get();
        
        // Get all financial years from sessions
        $financialYears = Session::whereNotNull('financial_year')
            ->distinct()
            ->orderBy('financial_year', 'desc')
            ->pluck('financial_year');
        
        $sessions = Session::orderBy('session')->get();
        
        $centers = Center::orderBy('center_no')->get(['center_no', 'center_name', 'level']);
        
        $components = Component::with('subject')
            ->orderBy('subject_code')
            ->orderBy('component_code')
            ->get(['id', 'component_name', 'subject_code', 'component_code']);

        $subjects = Subject::orderBy('subject_name')->get(['subject_code', 'subject_name']);

        return view('admin.stationery.index', compact('levels', 'financialYears', 'sessions', 'centers', 'components', 'subjects'));
    }

    /**
     * Get sessions filtered by level and financial year - AJAX endpoint
     */
    public function getSessionsByFilters(Request $request)
    {
        $level = $request->level;
        $financialYear = $request->financial_year;
        
        $sessions = Session::when($financialYear, function($q) use ($financialYear) {
                $q->where('financial_year', $financialYear);
            })
           
            ->orderBy('session')
            ->get();
        
        return response()->json([
            'success' => true,
            'sessions' => $sessions
        ]);
    }

    /**
     * Get centers filtered by level, financial year and session - AJAX endpoint
     */
    public function getCentersByFilters(Request $request)
    {
        $level = $request->level;
        $financialYear = $request->financial_year;
        $sessionId = $request->session_id;
        
        if (!$level || !$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Level and Session are required'
            ]);
        }
        
        $session = Session::find($sessionId);
        
        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found'
            ]);
        }
        
        // Get level details
        $levelData = Level::find($level);
        
        // Get centers that have candidates for this level, financial year and session
        $centers = DB::table('center_candidate')
            ->select('center_candidate.center_no', 'centers.center_name')
            ->join('centers', 'center_candidate.center_no', '=', 'centers.center_no')
            ->where('center_candidate.level', $levelData ? $levelData->description : $level)
            ->where('center_candidate.financial_year', $financialYear)
            ->where('center_candidate.session', $session->session)
            ->groupBy('center_candidate.center_no', 'centers.center_name')
            ->orderBy('center_candidate.center_no')
            ->get();
        
        \Log::info('Centers loaded for filters', [
            'level' => $levelData ? $levelData->description : $level,
            'financial_year' => $financialYear,
            'session' => $session->session,
            'centers_count' => $centers->count()
        ]);
        
        return response()->json([
            'success' => true,
            'centers' => $centers
        ]);
    }

    /**
     * Get components filtered by level, financial year, session and center - AJAX endpoint
     */
    public function getComponentsByFilters(Request $request)
    {
        $level = $request->level;
        $financialYear = $request->financial_year;
        $sessionId = $request->session_id;
        $centerNo = $request->center_no;
        
        if (!$level || !$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Level and Session are required'
            ]);
        }
        
        $session = Session::find($sessionId);
        
        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found'
            ]);
        }
        
        // Get level details
        $levelData = Level::find($level);
        
        // Get components that have candidates registered in this level, center, session, and financial year
        $componentsQuery = DB::table('center_candidate')
            ->select(
                'components.id',
                'components.component_name',
                'components.subject_code',
                'components.component_code',
                'subjects.subject_name',
                DB::raw('COUNT(DISTINCT center_candidate.candidate_no) as candidate_count')
            )
             
            ->join('candidate_subject', function($join) {
                $join->on('center_candidate.candidate_no', '=', 'candidate_subject.candidate_no')
                    ->on('center_candidate.session', '=', 'candidate_subject.session')
                    ->on('center_candidate.financial_year', '=', 'candidate_subject.financial_year')
                    ->on('center_candidate.level', '=', 'candidate_subject.level');
            })
             ->join('subjects', 'candidate_subject.subject_code', '=', 'subjects.subject_code')
            ->join('timetable', 'candidate_subject.subject_code', '=', 'timetable.subject_code')
            ->join('components', function($join) {
                $join->on('timetable.subject_code', '=', 'components.subject_code')
                    ->on('timetable.paper_no', '=', 'components.component_code');
            })
            ->where('center_candidate.level', $levelData ? $levelData->description : $level)
            ->where('center_candidate.financial_year', $financialYear)
            ->where('center_candidate.session', $session->session);
        
        if ($centerNo) {
            $componentsQuery->where('center_candidate.center_no', $centerNo);
        }
        
        $components = $componentsQuery
            ->groupBy('components.id', 'components.component_name', 'components.subject_code', 'components.component_code')
            ->orderBy('components.subject_code')
            ->orderBy('components.component_code')
            ->get();
        
        \Log::info('Components loaded', [
            'level' => $levelData ? $levelData->description : $level,
            'financial_year' => $financialYear,
            'session' => $session->session,
            'center_no' => $centerNo,
            'components_count' => $components->count()
        ]);
        
        return response()->json([
            'success' => true,
            'components' => $components
        ]);
    }

    /**
     * Generate allocation report
     */
   public function generateReport(Request $request)
    {
        try {
            $validated = $request->validate([
                'level' => 'required|exists:levels,id',
                'financial_year' => 'required|string',
                'session_id' => 'required|exists:sessions,id',
                'center_no' => 'required|exists:centers,center_no',
                'component_id' => 'nullable|exists:components,id',
            ]);

            \Log::info('Generating allocation report', $validated);

            $center = Center::where('center_no', $validated['center_no'])->first();
            
            if (!$center) {
                return response()->json([
                    'success' => false,
                    'message' => 'Center not found'
                ]);
            }

            $session = Session::find($validated['session_id']);
            
            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found'
                ]);
            }
            
            $level = Level::find($validated['level']);
            
            if (!$level) {
                return response()->json([
                    'success' => false,
                    'message' => 'Level not found'
                ]);
            }
            
            \Log::info('Session and level details', [
                'session_id' => $session->id,
                'session_name' => $session->session,
                'level_id' => $level->id,
                'level_description' => $level->description,
                'financial_year' => $validated['financial_year']
            ]);

            // Get candidate counts per component for this level, center, session, and financial year
            $componentCandidatesQuery = DB::table('center_candidate')
                ->select(
                    'components.id as component_id',
                    'components.component_name',
                    'components.subject_code',
                    'components.component_code',
                    DB::raw('COUNT(DISTINCT center_candidate.candidate_no) as candidate_count')
                )
                ->join('candidate_subject', function($join) {
                    $join->on('center_candidate.candidate_no', '=', 'candidate_subject.candidate_no')
                        ->on('center_candidate.session', '=', 'candidate_subject.session')
                        ->on('center_candidate.financial_year', '=', 'candidate_subject.financial_year')
                        ->on('center_candidate.level', '=', 'candidate_subject.level');
                })
                ->join('timetable', 'candidate_subject.subject_code', '=', 'timetable.subject_code')
                ->join('components', function($join) {
                    $join->on('timetable.subject_code', '=', 'components.subject_code')
                        ->on('timetable.paper_no', '=', 'components.component_code');
                })
                ->where('center_candidate.center_no', $validated['center_no'])
                ->where('center_candidate.level', $level->description)
                ->where('center_candidate.session', $session->session)
                ->where('center_candidate.financial_year', $validated['financial_year']);
            
            // Filter by specific component if provided
            if (isset($validated['component_id'])) {
                $componentCandidatesQuery->where('components.id', $validated['component_id']);
            }
            
            $componentCandidates = $componentCandidatesQuery
                ->groupBy('components.id', 'components.component_name', 'components.subject_code', 'components.component_code')
                ->get();
            
            $totalCandidates = $componentCandidates->sum('candidate_count');
            
            \Log::info('Component candidates result', [
                'center_no' => $validated['center_no'],
                'level' => $level->description,
                'session' => $session->session,
                'financial_year' => $validated['financial_year'],
                'components_with_candidates' => $componentCandidates->count(),
                'total_candidate_registrations' => $totalCandidates,
                'specific_component' => isset($validated['component_id']) ? 'Yes' : 'No'
            ]);
            
            if ($componentCandidates->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No candidates found registered for any components in this level, center, session, and financial year.'
                ]);
            }

            // Get number of unique candidates
            $uniqueCandidates = DB::table('center_candidate')
                ->where('center_no', $validated['center_no'])
                ->where('level', $level->description)
                ->where('session', $session->session)
                ->where('financial_year', $validated['financial_year'])
                ->distinct('candidate_no')
                ->count('candidate_no');
            
            $numInvigilators = $this->estimateInvigilators($uniqueCandidates);

            $allocations = [];
            $componentsWithoutRules = [];
            
            // If no specific component selected, get ALL components with allocation rules
            // and match them with candidate data
            if (!isset($validated['component_id'])) {
                \Log::info('No specific component selected - allocating for all components with rules');
                
                // Get all component IDs that have candidates
                $componentIdsWithCandidates = $componentCandidates->pluck('component_id')->toArray();
                
                // Get all components that have allocation rules configured
                $componentsWithRules = Component::whereHas('componentStocks', function($query) {
                    $query->where('is_active', true);
                })
                ->whereIn('id', $componentIdsWithCandidates)
                ->with(['componentStocks' => function($query) {
                    $query->where('is_active', true)->with('stockItem.stockType');
                }])
                ->get();
                
                \Log::info('Components with allocation rules', [
                    'total_components_with_candidates' => count($componentIdsWithCandidates),
                    'components_with_rules' => $componentsWithRules->count()
                ]);
                
                // Process each component that has both candidates AND allocation rules
                foreach ($componentsWithRules as $component) {
                    // Find the candidate data for this component
                    $componentCandidateData = $componentCandidates->firstWhere('component_id', $component->id);
                    
                    if (!$componentCandidateData) {
                        continue; // Skip if no candidates for this component
                    }
                    
                    $numCandidates = $componentCandidateData->candidate_count;
                    
                    foreach ($component->componentStocks as $componentStock) {
                        try {
                            // Calculate allocation based on actual candidates for this component
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
                                    'subject_code' => $component->subject_code,
                                    'full_code' => $component->subject_code . '-' . $component->component_code,
                                    'candidates_registered' => $numCandidates,
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
                                'component_id' => $component->id,
                                'component_stock_id' => $componentStock->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
                
                // Track components that have candidates but no allocation rules
                $componentIdsWithRules = $componentsWithRules->pluck('id')->toArray();
                foreach ($componentCandidates as $compData) {
                    if (!in_array($compData->component_id, $componentIdsWithRules)) {
                        $componentsWithoutRules[] = [
                            'component_id' => $compData->component_id,
                            'component_name' => $compData->component_name,
                            'subject_code' => $compData->subject_code,
                            'component_code' => $compData->component_code,
                            'candidate_count' => $compData->candidate_count,
                        ];
                    }
                }
                
            } else {
                // Specific component selected - original logic
                \Log::info('Specific component selected - allocating for single component');
                
                foreach ($componentCandidates as $componentCandidate) {
                    $component = Component::find($componentCandidate->component_id);
                    
                    if (!$component) {
                        continue;
                    }
                    
                    $numCandidates = $componentCandidate->candidate_count;
                    
                    // Get component stocks with allocation rules
                    $componentStocks = ComponentStock::where('component_id', $component->id)
                        ->where('is_active', true)
                        ->with(['stockItem.stockType'])
                        ->get();

                    \Log::info('Component stocks found', [
                        'component_id' => $component->id,
                        'component_name' => $component->component_name,
                        'candidates_registered' => $numCandidates,
                        'stocks_count' => $componentStocks->count()
                    ]);

                    if ($componentStocks->isEmpty()) {
                        \Log::warning('No allocation rules found for component', ['component_id' => $component->id]);
                        continue;
                    }

                    foreach ($componentStocks as $componentStock) {
                        try {
                            // Calculate allocation based on actual candidates for this component
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
                                    'subject_code' => $component->subject_code,
                                    'full_code' => $component->subject_code . '-' . $component->component_code,
                                    'candidates_registered' => $numCandidates,
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
            }
            
            \Log::info('Total allocations generated', [
                'allocations_count' => count($allocations),
                'components_without_rules_count' => count($componentsWithoutRules)
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'center' => [
                        'center_no' => $center->center_no,
                        'center_name' => $center->center_name,
                        'district' => $center->district,
                    ],
                    'level' => [
                        'id' => $level->id,
                        'description' => $level->description,
                    ],
                    'num_candidates' => $uniqueCandidates,
                    'total_component_registrations' => $totalCandidates,
                    'num_invigilators' => $numInvigilators,
                    'allocations' => $allocations,
                    'components_without_rules' => $componentsWithoutRules,
                    'session' => [
                        'id' => $session->id,
                        'session' => $session->session,
                        'financial_year' => $validated['financial_year'],
                        'description' => $session->description ?? null,
                    ],
                    'component' => isset($validated['component_id']) 
                        ? Component::with('subject')->find($validated['component_id']) 
                        : null,
                ]
            ]);
            
        } catch (\ValidationException $e) {
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
            'allocations.*.num_candidates' => 'nullable|integer|min:0',
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
                        'center_no' => $validated['center_no'],
                        'session_id' => $validated['session_id'],
                        'component_id' => $allocationData['component_id'],
                        'stock_item_id' => $allocationData['stock_item_id'],
                    ],
                    [
                        'quantity_allocated' => $allocationData['allocated_qty'],
                        'num_candidates' => $allocationData['num_candidates'] ?? 0,
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
                $q->where('center_no', $request->center_no);
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
        $levels = Level::where('is_active', true)->orderBy('id')->get();

        return view('admin.stationery.allocation.view', compact('centers', 'sessions', 'levels'));
    }
    //                             
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
}