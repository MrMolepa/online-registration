<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class SubjectGroupRule extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'rule_name',
        'level_id',
        'type',
        'rules',
        'is_active',
    ];

    protected $casts = [
        'rules' => 'array',
        'is_active' => 'boolean',
        'type' => 'integer',
    ];

    protected static $recordEvents = ['created', 'updated', 'deleted'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logAttributes = [
        'rule_name',
        'level_id',
        'type',
        'is_active'
    ];

    /**
     * Get the level that owns the rule
     */
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    /**
     * Scope for active rules
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }



    /**
     * Scope for specific level
     */
    public function scopeForLevel($query, $levelId)
    {
        return $query->where('level_id', $levelId);
    }

    /**
     * Scope for specific type
     */
    public function scopeForType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get required group codes
     */
    public function getRequiredGroupsAttribute()
    {
        return $this->rules['required_groups'] ?? [];
    }

    /**
     * Get forbidden group codes
     */
    public function getForbiddenGroupsAttribute()
    {
        return $this->rules['forbidden_groups'] ?? [];
    }

    /**
     * Get minimum subjects count
     */
    public function getMinSubjectsAttribute()
    {
        return $this->rules['min_subjects'] ?? null;
    }

    /**
     * Get maximum subjects count
     */
    public function getMaxSubjectsAttribute()
    {
        return $this->rules['max_subjects'] ?? null;
    }

    /**
     * Get group constraints
     */
    public function getGroupConstraintsAttribute()
    {
        return $this->rules['group_constraints'] ?? [];
    }

    /**
     * Get type name
     */
    public function getTypeNameAttribute()
    {
        $types = [
            1 => 'Full Registration',
            2 => 'Partial Registration',
            3 => 'Private Registration',
        ];
        return $types[$this->type] ?? 'Unknown';
    }
}