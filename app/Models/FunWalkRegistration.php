<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FunWalk;
use App\Models\FunWalkPayment;

class FunWalkRegistration extends Model
{
    use HasFactory;

    protected $table = 'fun_walk_registrations';

    protected $fillable = [
        'fun_walk_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'email',
        'phone',
        'ticket_number',
        'qr_path'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    // Gender constants
    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';
    const GENDER_OTHER = 'other';

    /**
     * Relationship: A registration belongs to a fun walk
     */
    public function funWalk()
    {
        return $this->belongsTo(FunWalk::class);
    }

    /**
     * Relationship: A registration has many payments
     */
    public function payments()
    {
        return $this->hasMany(FunWalkPayment::class, 'registration_id');
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Scope to get registrations for a specific fun walk
     */
    public function scopeForFunWalk($query, $funWalkId)
    {
        return $query->where('fun_walk_id', $funWalkId);
    }
}