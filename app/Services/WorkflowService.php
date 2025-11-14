<?php

namespace App\Services;

use App\Models\Workflow;
use App\Models\WorkflowInstance;
use App\Models\WorkflowInstanceStep;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class WorkflowService
{
    /**
     * Start a new workflow instance
     *
     * @param int $workflowId
     * @param string $entityType
     * @param int $userId
     * @return WorkflowInstance
     */
    public function startWorkflow($workflowId, $entityType, $entityId, $userId)
    {
        $workflow = Workflow::findOrFail($workflowId);

        $instance = WorkflowInstance::create([
            'workflow_id' => $workflow->id,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'status' => 'pending',
            'created_by' => $userId,
        ]);

        $this->initializeWorkflowSteps($instance);
        $this->processNextStep($instance);

        return $instance;
    }

    /**
     * Start workflow for an arbitrary Eloquent entity when context is inferable or optional.
     * Falls back to nulls where not supplied.
     */
    public function startWorkflowForEntity($workflowId, $entity, $userId = null)
    {
        $workflow = Workflow::findOrFail($workflowId);

        $instance = WorkflowInstance::create([
            'workflow_id' => $workflow->id,
            'entity_type' => get_class($entity),
            'entity_id' => $entity->getKey(),
            'status' => 'pending',
            'created_by' => $userId,
        ]);

        $this->initializeWorkflowSteps($instance);
        $this->processNextStep($instance);
        return $instance;
    }

    /**
     * Initialize all steps for a workflow instance
     *
     * @param WorkflowInstance $instance
     * @return void
     */
    protected function initializeWorkflowSteps(WorkflowInstance $instance)
    {
        $steps = $instance->workflow->steps()->orderBy('order')->get();

        foreach ($steps as $step) {
            WorkflowInstanceStep::create([
                'instance_id' => $instance->id,
                'workflow_step_id' => $step->id,
                'status' => 'pending',
                'token' => Str::random(64),
                'expires_at' => now()->addDays(7), // 7 days to respond
            ]);
        }
    }

    /**
     * Process the next step in the workflow
     *
     * @param WorkflowInstance $instance
     * @return void
     */
    public function processNextStep(WorkflowInstance $instance)
    {
        $currentStep = $instance->steps()
            ->where('status', 'pending')
            ->orderBy('id')
            ->first();



        if (!$currentStep) {
            // No more steps, workflow is complete
            $instance->update(['status' => 'completed']);
            return;
        }

        // Update instance status to in_progress if this is the first step
        if ($instance->status === 'pending') {
            $instance->update(['status' => 'in_progress']);
        }

        $this->notifyApprovers($currentStep);
    }

    /**
     * Notify approvers for a workflow step
     *
     * @param WorkflowInstanceStep $step
     * @return void
     */
    protected function notifyApprovers(WorkflowInstanceStep $step)
    {
        $instance = $step->instance;
        $workflowStep = $step->step;
        $role = $workflowStep->role;

        // // Get users with the required role
        // $users = User::whereHas('roles', function ($query) use ($role) {
        //     $query->where('id', $role->id);
        // })->get();

        // // Update step with notification timestamp
        // $step->update([
        //     'notified_at' => now(),
        //     'expires_at' => now()->addDays(7), // Reset expiration when notifying
        // ]);

        // Send notifications
        // foreach ($users as $user) {
        //     Mail::to($user->email)->queue(
        //         new WorkflowNotification($step, $user)
        //     );
        // }
    }

    /**
     * Process approval for a workflow step
     *
     * @param WorkflowInstanceStep $step
     * @param User $user
     * @param string $status
     * @param string|null $comments
     * @return WorkflowInstance
     */
    public function processApproval(WorkflowInstanceStep $step, User $user, $status, $comments = null)
    {
        // Update the step
        $step->update([
            'status' => $status,
            'comments' => $comments,
            'action_by' => $user->id,
            'action_at' => now(),
        ]);

        $instance = $step->instance;

        if ($status === 'rejected') {
            // If rejected, mark workflow as rejected
            $instance->update(['status' => 'rejected']);
            return $instance;
        }

        // Process next step
        $this->processNextStep($instance);

        return $instance;
    }

    /**
     * Process the current pending step for a workflow instance (admin-side helper)
     *
     * @param int $instanceId
     * @param string $action           approve|reject
     * @param string|null $comments
     * @return WorkflowInstance
     * @throws \Exception
     */
    public function processStep($instanceId, $action, $comments = null)
    {

        // Load workflow instance with pending steps and their step details
        $instance = WorkflowInstance::with([
            'steps' => function ($q) {
                $q->where('status', 'pending')->orderBy('id');
            },
            'steps.step' // eager load WorkflowStep details
        ])->findOrFail($instanceId);






        $user = auth()->user();
        if (!$user) {
            return;
        }




        // Find the first step this user is authorized for
        $step = $instance->steps->first(function ($stepInstance) use ($user) {
            return $stepInstance->step && $stepInstance->step->isAssignedTo($user);
        });




        if (!$step) {
            return;
        }


        // Proceed with processing the step using your helper
        return $this->processApproval($step, $user, $action, $comments);
    }





    /**
     * Get pending approvals for a user
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPendingApprovals(User $user)
    {
        $roleIds = $user->roles->pluck('id');

        return WorkflowInstanceStep::whereIn('workflow_step_id', function ($query) use ($roleIds) {
            $query->select('id')
                ->from('workflow_steps')
                ->whereIn('role_id', $roleIds);
        })
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->with(['instance', 'instance.workflow', 'instance.entity', 'step.role'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get pending approvals for a user by user ID (legacy method for admin compatibility)
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserPendingApprovals($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return collect();
        }

        return $this->getPendingApprovals($user);
    }
}
