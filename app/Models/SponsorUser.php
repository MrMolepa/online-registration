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

class SponsorUser  extends Authenticatable
{
    use HasFactory;
    use LaratrustUserTrait,
        HasApiTokens,
        HasFactory,
        Notifiable,
        CausesActivity,
        LogsActivity;

    protected $table = "sponsors";
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
        'level',
        'sponsor',
        'occupation',
        'center_name',
        'email',
        'password',
    ];


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
        'sponsor',
        'level',
        'username',
        'profile',
        'status'

    ];



    public function districts()
    {
        return $this->belongsToMany(District::class, 'district_sponsor','user_id','district_code');
    }

    public function roles()
    {
        return $this->morphMany(SponsorUser::class,'roleable');
    }


    

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
}
