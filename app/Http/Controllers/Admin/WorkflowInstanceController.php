<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workflow;
use App\Models\WorkflowInstance;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkflowInstanceController extends Controller
{
    protected $workflowService;


    public function start(Request $request)
    {
        $validated = $request->validate([
            'workflow_id' => 'required|exists:workflows,id',
            'entity_type' => 'required|string',
            'entity_id' => 'required|integer',
        ]);

        $entity = $this->getEntity($validated['entity_type'], $validated['entity_id']);

        if (!$entity) {
            return response()->json(['message' => 'Entity not found'], 404);
        }

        try {
            $instance = $this->workflowService->startWorkflowForEntity(
                $validated['workflow_id'],
                $entity,
                Auth::guard('admin')->id()
            );

            return redirect()->back()
                ->with('success', 'Workflow started successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to start workflow: ' . $e->getMessage());
        }
    }

    public function show(WorkflowInstance $instance)
    {
        $instance->load([
            'workflow',
            'steps.step.role',
            'steps.actor'
        ]);

        return view('workflows.instances.show', compact('instance'));
    }

    protected function getEntity($entityType, $entityId)
    {
        if (!class_exists($entityType)) {
            return null;
        }

        return $entityType::find($entityId);
    }
}
