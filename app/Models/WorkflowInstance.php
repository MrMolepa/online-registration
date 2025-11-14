<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowInstance extends Model
{
    // app/Models/WorkflowInstance.php
protected $fillable = [
    'workflow_id',
    'entity_type',
    'entity_id',
    'status',
    'created_by',
    'completed_at'
];

protected $casts = [
    'completed_at' => 'datetime'
];

public function workflow()
{
    return $this->belongsTo(Workflow::class);
}

public function steps()
{
    return $this->hasMany(WorkflowInstanceStep::class, 'instance_id');
}

public function entity()
{
    return $this->morphTo();
}

public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}

public function currentStep()
{
    return $this->steps()
        ->where('status', 'pending')
        ->orderBy('order')
        ->first();
}

public function isCompleted()
{
    return $this->status === 'completed';
}

public function isRejected()
{
    return $this->status === 'rejected';
}

public function isPending()
{
    return $this->status === 'pending';
}

public function isInProgress()
{
    return $this->status === 'in_progress';
}
}
