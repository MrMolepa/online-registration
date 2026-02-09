<?php
// app/Models/Permission.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Permission extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'display_name',
        'description'
    ];

    /**
     * Get the roles that have this permission
     * Note: No withTimestamps() since permission_role table doesn't have timestamps
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role');
    }

    /**
     * Get users who have this permission explicitly assigned
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'permission_user')
                    ->withPivot('allowed');
    }

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'display_name', 'description'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}