<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class SubjectGroup extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'group_code',
        'group_name',
        'level_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static $recordEvents = ['created', 'updated', 'deleted'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected static $logAttributes = [
        'group_code',
        'group_name',
        'level_id',
        'is_active'
    ];

    /**
     * Get the level that owns the subject group
     */
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    /**
     * Get the subjects in this group
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_group_subject', 'subject_group_id', 'subject_code');
    }

    /**
     * Scope for active groups
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific financial year
     */
    public function scopeForFinancialYear($query, $financialYear)
    {
        return $query->where('financial_year', $financialYear);
    }

    /**
     * Scope for specific level
     */
    public function scopeForLevel($query, $levelId)
    {
        return $query->where('level_id', $levelId);
    }

    /**
     * Get subject codes in this group
     */
    public function getSubjectCodesAttribute()
    {
        return $this->subjects->pluck('subject_code')->toArray();
    }
}