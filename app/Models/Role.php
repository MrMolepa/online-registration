<?php
// app/Models/Role.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Role extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'display_name',
        'description'
    ];

    /**
     * Get all users assigned to this role
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all permissions assigned to this role
     * Note: No withTimestamps() since permission_role table doesn't have timestamps
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    /**
     * Check if role has a specific permission
     *
     * @param string $permissionSlug
     * @return bool
     */
    public function hasPermission($permissionSlug)
    {
        return $this->permissions()
                    ->where('permissions.name', $permissionSlug)
                    ->exists();
    }

    /**
     * Assign a permission to this role
     *
     * @param int|Permission $permission
     * @return void
     */
    public function givePermission($permission)
    {
        $permissionId = $permission instanceof Permission ? $permission->id : $permission;
        
        if (!$this->permissions()->where('permission_id', $permissionId)->exists()) {
            $this->permissions()->attach($permissionId);
        }
    }

    /**
     * Remove a permission from this role
     *
     * @param int|Permission $permission
     * @return void
     */
    public function revokePermission($permission)
    {
        $permissionId = $permission instanceof Permission ? $permission->id : $permission;
        $this->permissions()->detach($permissionId);
    }

    /**
     * Sync permissions for this role (compatible with existing code)
     *
     * @param array $permissions
     * @return void
     */
    public function syncPermissions(array $permissions)
    {
        $this->permissions()->sync($permissions);
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