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



     /**
     * Get all of the post's comments.
     */




    public function admins()
    {
        return $this->morphedByMany(AdminUser::class, 'user','role_user','role_id','user_id','id','id');
    }

    public function centers()
    {
        return $this->morphedByMany(User::class, 'user','role_user','role_id','user_id','id','id');
    }


    public function sponosors()
    {
        return $this->morphedByMany(SponsorUser::class, 'user','role_user','role_id','user_id','id','id');
    }








}
