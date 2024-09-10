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
use Illuminate\Database\Eloquent\Model;

class CandidateUser extends Authenticatable
{
    use HasFactory;
    use HasApiTokens,
        HasFactory,
        Notifiable,
        CausesActivity,
        LogsActivity;
    protected $table = 'candidate_users';
    protected $guarded=[];
    protected $fillable = [
        'username',
        'national_id',
        'center_no',
        'candidate_no',
        'candidate_password',
        'profile',
        'status',
        'email',
        'session',
        'session',
        'financial_year',
        'password',
    ];
    protected $hidden = ['password',  'remember_token'];

    public function roleable()
    {
        return $this->hasMany(Role::class);
    }
}
