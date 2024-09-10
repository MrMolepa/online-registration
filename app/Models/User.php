<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laratrust\Traits\LaratrustUserTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\LogOptions;


class User extends Authenticatable
{
    use LaratrustUserTrait,
        HasApiTokens,
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
    ];

    protected static $recordEvents = ['created', 'updated', 'deleted'];

    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;


    protected static $logAttributes = [
        'profile',
        'status',
        'occupation',
        'center_name',
        'email',
    ];

    protected $guarded=[];


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


    public function center()
    {
        return $this->belongsTo(Center::class, 'center_no');
    }


    public function document_user_profile()
    {
        return $this->morphToMany(DocumentUser::class, 'document_user');
    }




    //


    public function roles()
    {
        return $this->morphMany(User::class,'roleable');
    }
}
