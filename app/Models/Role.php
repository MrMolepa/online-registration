<?php

namespace App\Models;

use Laratrust\Models\LaratrustRole;
use Spatie\Activitylog\Traits\LogsActivity;

class Role extends LaratrustRole
{
    use LogsActivity;


    public $guarded = [];
    protected  $fillable = [
        'name', 'display_name', 'description'
    ];


    protected static $recordEvents = ['created', 'updated', 'deleted'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;


    protected static $logAttributes = [
        'name', 'display_name', 'description'
    ];
    // In Role.php
    public function menuPermissions()
    {
        return $this->belongsToMany(Permission::class, 'menu_permission')
                    ->withPivot('menu_id')
                    ->withTimestamps();
    }

    public function menusWithPermission($permissionId)
    {
        return $this->belongsToMany(Menu::class, 'menu_permission')
                    ->wherePivot('permission_id', $permissionId);
    }


    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_role');
    }

     /**
     * Get all of the post's comments.
     */













}
