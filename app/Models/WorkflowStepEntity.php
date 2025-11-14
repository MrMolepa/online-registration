<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowStepEntity extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'workflow_step_id',
        'entity_id',
        'entity_type',
    ];

    /**
     * Get the parent entity model.
     */
    public function entity()
    {
        return $this->morphTo();
    }


    public function assignee()
    {
        return $this->morphTo(); // User, Role
    }

    /**
     * Get the workflow step that owns the entity assignment.
     */
    public function workflowStep()
    {
        return $this->belongsTo(WorkflowStep::class);
    }
}
