<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowInstanceStep extends Model
{
    // app/Models/WorkflowInstanceStep.php
protected $fillable = [
    'instance_id',
    'workflow_step_id',
    'status',
    'comments',
    'action_by',
    'action_at',
    'metadata',
    'token',
    'notified_at',
    'expires_at'
];

protected $casts = [
    'metadata' => 'array',
    'action_at' => 'datetime',
    'notified_at' => 'datetime',
    'expires_at' => 'datetime'
];



public function instance()
{
    return $this->belongsTo(WorkflowInstance::class, 'instance_id');
}

public function step()
{
    return $this->belongsTo(WorkflowStep::class, 'workflow_step_id');
}

public function actor()
{
    return $this->belongsTo(User::class, 'action_by');
}

public function isPending()
{
    return $this->status === 'pending';
}

public function isApproved()
{
    return $this->status === 'approved';
}

public function isRejected()
{
    return $this->status === 'rejected';
}

public function isExpired()
{
    return $this->expires_at && $this->expires_at->isPast();
}
}
