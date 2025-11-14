<?php

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

class AdminUser  extends Authenticatable
{
    use LaratrustUserTrait,
        HasApiTokens,
        HasFactory,
        Notifiable,
        CausesActivity,
        LogsActivity,
        Notifiable;

    protected $table = "admins";
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'username',
        'profile',
        'status',
        'occupation',
        'email',
        'password',
    ];
    protected $guarded=[];


    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */


    protected static $recordEvents = ['created', 'updated', 'deleted'];

    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;


    protected static $logAttributes = [
        'profile',
        'status',
        'occupation',
        'email',
        'username',
        'profile',
        'status'

    ];

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

    public function document_user_profile()
    {
        return $this->morphOne(DocumentUser::class, 'document_user');
    }



}
