<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasApiTokens,
        HasFactory,
        Notifiable,
        CausesActivity,
        LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_type',
        'username',
        'center_no',
        'centre_account_password',
        'profile',
        'status',
        'occupation',
        'center_name',
        'email',
        'password',
        'role_id',
    ];

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the role assigned to this user
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get user-specific permissions (with allow/deny flag)
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user')
                    ->withPivot('allowed');
    }

    /**
     * Core permission checking method
     * Priority: User-specific permissions > Role permissions > Default (false)
     *
     * @param string $permissionSlug
     * @return bool
     */
    public function hasPermission($permissionSlug)
    {
        // Step 1: Check user-specific permissions first (highest priority)
        $userPermission = $this->permissions()
                              ->where('permissions.name', $permissionSlug)
                              ->first();

        if ($userPermission !== null) {
            // User has explicit permission - return the allowed status
            return (bool) $userPermission->pivot->allowed;
        }

        // Step 2: Check role permissions if user has a role
        if ($this->role) {
            return $this->role->hasPermission($permissionSlug);
        }

        // Step 3: Default to false (no permission)
        return false;
    }

    /**
     * Assign a user-specific permission (allow or deny)
     *
     * @param int|Permission $permission
     * @param bool $allowed
     * @return void
     */
    public function givePermission($permission, $allowed = true)
    {
        $permissionId = $permission instanceof Permission ? $permission->id : $permission;
        
        $this->permissions()->syncWithoutDetaching([
            $permissionId => ['allowed' => $allowed]
        ]);
    }

    /**
     * Remove a user-specific permission
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
     * Get all effective permissions for this user
     * Combines role permissions with user-specific overrides
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllPermissions()
    {
        $permissions = collect();

        // Get role permissions
        if ($this->role) {
            $rolePermissions = $this->role->permissions->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'display_name' => $permission->display_name,
                    'description' => $permission->description,
                    'source' => 'role',
                    'allowed' => true,
                ];
            });
            $permissions = $permissions->merge($rolePermissions);
        }

        // Get user-specific permissions (these override role permissions)
        $userPermissions = $this->permissions->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'display_name' => $permission->display_name,
                'description' => $permission->description,
                'source' => 'user',
                'allowed' => (bool) $permission->pivot->allowed,
            ];
        });

        // Merge and ensure user permissions override role permissions
        foreach ($userPermissions as $userPerm) {
            $permissions = $permissions->reject(function ($perm) use ($userPerm) {
                return $perm['id'] === $userPerm['id'];
            });
            $permissions->push($userPerm);
        }

        return $permissions;
    }

    /**
     * Check if user has ANY of the given permissions
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAnyPermission(array $permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has ALL of the given permissions
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAllPermissions(array $permissions)
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    public function center()
    {
        return $this->belongsTo(Center::class, 'center_no');
    }

    public function document_user_profile()
    {
        return $this->morphOne(DocumentUser::class, 'document_user');
    }

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['profile', 'status', 'occupation', 'center_name', 'email'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}