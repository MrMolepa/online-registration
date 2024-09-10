<?php

namespace App\Models;

use Laratrust\Models\LaratrustPermission;
use Spatie\Activitylog\Traits\LogsActivity;

class Permission extends LaratrustPermission
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
}
