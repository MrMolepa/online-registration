<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use App\Models\WorkflowStepEntity;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WorkflowController extends Controller
{


    public function index()
    {
        $workflows = Workflow::withCount('steps')->latest()->paginate(10);



        $entityTypes = $this->getEntityTypes();
        $roles = Role::pluck('name', 'id');



        $allUsers = collect();

        foreach (Config::get('auth.guards') as $guard => $settings) {
            $provider = Config::get("auth.guards.$guard.provider");
            $model = Config::get("auth.providers.$provider.model");
            if (class_exists($model)) {
                $users = $model::select('id', 'username as name')->get()->map(function ($user) use ($guard) {
                    $user->guard = $guard; // Tag user with their guard name
                    return $user;
                });

                $allUsers = $allUsers->concat($users);
            }
        }

        $users= $allUsers;

        return view('admin.workflows.index', compact('workflows', 'entityTypes', 'roles', 'users'));
    }

    public function create()
    {
        // Redirect to index and open the create modal there
        return redirect()->route('admin.workflows.index')->with('openCreateModal', true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'entity_type' => 'required|string',
            'steps' => 'required|array|min:1',
            'steps.*.name' => 'required|string|max:255',
            'steps.*.entity_ids' => 'required|array|min:1',
            'steps.*.entity_ids.*' => 'required|string',
            'steps.*.is_mandatory' => 'sometimes|boolean',
        ]);

        DB::transaction(function () use ($validated) {
            $workflow = Workflow::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'entity_type' => $validated['entity_type'],
                'created_by' => auth('admin')->id(),
            ]);

            foreach ($validated['steps'] as $index => $stepData) {
                $step = $workflow->steps()->create([
                    'name' => $stepData['name'],
                    'order' => $index + 1,
                    'is_mandatory' => !empty($stepData['is_mandatory']),
                ]);

                // Process entity assignments
                $this->processStepEntities($step, $stepData['entity_ids']);
            }
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => 'Workflow created successfully.'], 201);
        }

        return redirect()->route('admin.workflows.index')
            ->with('success', 'Workflow created successfully.');
    }

    public function edit(Request $request, Workflow $workflow)
    {
        $entityTypes = $this->getEntityTypes();
        $roles = Role::pluck('name', 'id');
        // Some installations store a single username column; fall back to that to avoid missing column errors
        $users = User::select('id', 'username as name')
            ->pluck('name', 'id');

        $workflow->load(['steps.entities', 'steps.roles', 'steps.users']);

        if ($request->ajax()) {
            return view('admin.workflows.edit_form', compact('workflow', 'entityTypes', 'roles', 'users'));
        }

        // For non-AJAX, redirect back to index and open edit modal for this workflow
        return redirect()->route('admin.workflows.index')->with('openEditModalId', $workflow->id);
    }

    /**
     * Show the steps for a workflow (standalone page)
     */
    public function steps(Workflow $workflow)
    {
        // Load steps and related role/user entities as needed
        $workflow->load(['steps.roles', 'steps.users']);
        return view('admin.workflows.steps', compact('workflow'));
    }

    public function update(Request $request, Workflow $workflow)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'entity_type' => ['required', 'string', Rule::in(array_keys($this->getEntityTypes()))],
            'is_active' => 'sometimes|boolean',
            'steps' => 'required|array|min:1',
            'steps.*.id' => 'nullable|exists:workflow_steps,id',
            'steps.*.name' => 'required|string|max:255',
            'steps.*.entity_ids' => 'required|array|min:1',
            'steps.*.entity_ids.*' => 'required|string',
            'steps.*.is_mandatory' => 'sometimes|boolean',
        ]);

        $isActive = $request->boolean('is_active');

        DB::transaction(function () use ($workflow, $validated, $isActive) {
            $workflow->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'entity_type' => $validated['entity_type'],
                'is_active' => $isActive,
            ]);

            $existingStepIds = [];

            foreach ($validated['steps'] as $index => $stepData) {
                $step = $workflow->steps()->updateOrCreate(
                    ['id' => $stepData['id'] ?? null],
                    [
                        'name' => $stepData['name'],
                        'order' => $index + 1,
                        'is_mandatory' => !empty($stepData['is_mandatory']),
                    ]
                );

                // Process entity assignments
                $this->processStepEntities($step, $stepData['entity_ids']);

                $existingStepIds[] = $step->id;
            }

            // Delete steps that were removed
            $workflow->steps()->whereNotIn('id', $existingStepIds)->delete();
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => 'Workflow updated successfully.']);
        }

        return redirect()->route('admin.workflows.index')
            ->with('success', 'Workflow updated successfully.');
    }

    public function destroy(Workflow $workflow)
    {
        $workflow->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => 'Workflow deleted successfully.']);
        }

        return redirect()->route('admin.workflows.index')
            ->with('success', 'Workflow deleted successfully.');
    }

    /**
     * Process step entities and create/update assignments
     */
    protected function processStepEntities($step, array $entityIds)
    {
        // Delete existing entities for this step
        $step->entities()->delete();

        foreach ($entityIds as $entityId) {
            [$type, $id] = explode('_', $entityId, 2);
            $entityType = $type === 'role' ? Role::class : User::class;

            $step->entities()->create([
                'entity_id' => $id,
                'entity_type' => $entityType,
            ]);
        }
    }

    /**
     * Get available entity types for workflows
     */
    protected function getEntityTypes()
    {
        return [
            'App\\Models\\Invitation' => 'Invitation',
            // Add more entity types as needed
        ];
    }
}
