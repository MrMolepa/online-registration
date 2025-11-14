<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{

protected $fillable = [
    'name', 
    'description', 
    'entity_type', 
    'is_active',
    'created_by'
];

protected $casts = [
    'is_active' => 'boolean'
];

public function steps()
{
    return $this->hasMany(WorkflowStep::class)->orderBy('order');
}

public function instances()
{
    return $this->hasMany(WorkflowInstance::class);
}

public function scopeActive($query)
{
    return $query->where('is_active', true);
}
}