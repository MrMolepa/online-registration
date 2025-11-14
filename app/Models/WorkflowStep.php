<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_id',
        'name',
        'order',
        'is_mandatory',
        'actions'
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'actions' => 'array'
    ];

    /**
     * Get the workflow that owns the step.
     */
    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }

    /**
     * Get all entities assigned to this workflow step.
     */
    public function entities()
    {
        return $this->hasMany(WorkflowStepEntity::class);
    }



    public function getAssigneesByTypeAttribute()
    {
        return $this->assignments
            ->groupBy('entity_type')
            ->map(function ($group) {
                return $group->pluck('entity_id')->values();
            })
            ->map(function ($id, $type) {
                return [
                    'entity_type' => class_basename($type),
                    'entity_ids' => $id,
                ];
            })
            ->values();
    }







    public function isAssignedTo($user)
    {
        // Get the IDs of roles the user belongs to
        $userRoleIds = $user->roles()->pluck('id')->toArray();

        // Check if the user is directly assigned
        $directAssignment = $this->assignments()
            ->where('entity_type', get_class($user))
            ->where('entity_id', $user->id)
            ->exists();

        // Check if the user has a role that is assigned
        $roleAssignment = $this->assignments()
            ->where('entity_type', Role::class) // Role assignments
            ->whereIn('entity_id', $userRoleIds)
            ->exists();


        // User is assigned if either direct or role assignment exists
        return $directAssignment || $roleAssignment;
    }



    public function assignments()
    {
        return $this->hasMany(WorkflowStepEntity::class);
    }

    /**
     * Get all roles assigned to this workflow step.
     */
    public function roles()
    {
        return $this->morphedByMany(Role::class, 'entity', 'workflow_step_entities');
    }

    /**
     * Get all instance steps for this workflow step.
     */
    public function instanceSteps()
    {
        return $this->hasMany(WorkflowInstanceStep::class, 'workflow_step_id');
    }

    /**
     * Check if a specific entity is assigned to this step.
     *
     * @param  mixed  $entity
     * @return bool
     */
    public function hasEntity($entity)
    {
        if (is_string($entity)) {
            $entity = Role::where('name', $entity)->first();
        }

        if ($entity instanceof User) {
            return $this->users()->where('entity_id', $entity->id)->exists();
        }

        if ($entity instanceof Role) {
            return $this->roles()->where('entity_id', $entity->id)->exists();
        }

        return false;
    }
}
