<?php
// app/Models/AdminUser.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laratrust\Traits\LaratrustUserTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\LogOptions;

class AdminUser extends Authenticatable
{
    use LaratrustUserTrait,
        HasApiTokens,
        HasFactory,
        Notifiable,
        CausesActivity,
        LogsActivity;

    protected $table = "admins";
    
    protected $fillable = [
        'username',
        'profile',
        'status',
        'occupation',
        'email',
        'password',
        'role_id',
    ];
    
    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * =====================================================
     * RELATIONSHIPS
     * =====================================================
     */

    /**
     * Get the role assigned to this user (direct relationship)
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get user-specific permissions (with allow/deny flag)
     * Works with Laratrust's polymorphic permission_user table
     */
    public function userPermissions()
    {
        return $this->belongsToMany(
            Permission::class, 
            'permission_user', 
            'user_id', 
            'permission_id'
        )
        ->wherePivot('user_type', self::class) // Laratrust uses user_type
        ->withPivot('allowed');
    }

    /**
     * Alias for compatibility - returns user-specific permissions
     */
    public function permissions()
    {
        // Check if 'allowed' column exists in permission_user table
        try {
            return $this->userPermissions();
        } catch (\Exception $e) {
            // Fallback to Laratrust's default permissions relationship
            // This uses LaratrustUserTrait's permissions() method
            return $this->morphToMany(
                Permission::class,
                'user',
                'permission_user',
                'user_id',
                'permission_id'
            );
        }
    }

    public function document_user_profile()
    {
        return $this->morphOne(DocumentUser::class, 'document_user');
    }

    /**
     * =====================================================
     * PERMISSION METHODS
     * =====================================================
     */

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
        try {
            $userPermission = $this->userPermissions()
                                  ->where('permissions.name', $permissionSlug)
                                  ->first();

            if ($userPermission !== null) {
                // Check if 'allowed' column exists
                if (isset($userPermission->pivot->allowed)) {
                    return (bool) $userPermission->pivot->allowed;
                }
                // If no 'allowed' column, assume true (permission exists = allowed)
                return true;
            }
        } catch (\Exception $e) {
            // If userPermissions fails, continue to role check
        }

        // Step 2: Check direct role permissions
        if ($this->role) {
            if ($this->role->hasPermission($permissionSlug)) {
                return true;
            }
        }

        // Step 3: Fallback to Laratrust for backward compatibility
        if (method_exists($this, 'hasRole')) {
            // Use Laratrust's built-in permission checking
            $laratrustRoles = $this->roles;
            foreach ($laratrustRoles as $role) {
                $rolePermissions = $role->permissions;
                foreach ($rolePermissions as $permission) {
                    if ($permission->name === $permissionSlug) {
                        return true;
                    }
                }
            }
        }

        // Step 4: Default to false (no permission)
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
    
    try {
        \Log::info('Attempting to give permission', [
            'user_id' => $this->id,
            'permission_id' => $permissionId,
            'allowed' => $allowed
        ]);

        // Check if permission_user table has 'allowed' column
        $hasAllowedColumn = \Schema::hasColumn('permission_user', 'allowed');
        
        if ($hasAllowedColumn) {
            // Try with allowed column
            $this->permissions()->syncWithoutDetaching([
                $permissionId => [
                    'user_type' => self::class,
                    'allowed' => $allowed
                ]
            ]);
        } else {
            // Fallback: Just attach/detach without 'allowed' column
            if ($allowed) {
                $this->permissions()->syncWithoutDetaching([
                    $permissionId => ['user_type' => self::class]
                ]);
            } else {
                $this->permissions()->detach($permissionId);
            }
        }
        
        \Log::info('Permission given successfully');
        
    } catch (\Exception $e) {
        \Log::error('Error in givePermission: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
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
        
        try {
            $this->userPermissions()->detach($permissionId);
        } catch (\Exception $e) {
            $this->permissions()->detach($permissionId);
        }
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

        // Get role permissions (from direct role relationship)
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
        try {
            $userPermissions = $this->userPermissions->map(function ($permission) {
                $allowed = isset($permission->pivot->allowed) 
                    ? (bool) $permission->pivot->allowed 
                    : true;
                    
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'display_name' => $permission->display_name,
                    'description' => $permission->description,
                    'source' => 'user',
                    'allowed' => $allowed,
                ];
            });

            // Merge and ensure user permissions override role permissions
            foreach ($userPermissions as $userPerm) {
                $permissions = $permissions->reject(function ($perm) use ($userPerm) {
                    return $perm['id'] === $userPerm['id'];
                });
                $permissions->push($userPerm);
            }
        } catch (\Exception $e) {
            // If userPermissions fails, just use role permissions
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

    /**
     * =====================================================
     * LARATRUST COMPATIBILITY METHODS
     * =====================================================
     */

    /**
     * Sync roles - works with both Laratrust and direct role relationship
     *
     * @param array $roles
     * @return void
     */
    public function syncRoles($roles)
    {
        // Sync with Laratrust pivot table (role_user)
        if (method_exists($this, 'roles')) {
            $this->roles()->sync($roles);
        }
        
        // Also update role_id for direct relationship
        if (is_array($roles) && count($roles) > 0) {
            $this->role_id = $roles[0];
            $this->save();
        } elseif (!is_array($roles)) {
            $this->role_id = $roles;
            $this->save();
        }
    }

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['profile', 'status', 'occupation', 'email', 'username'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}