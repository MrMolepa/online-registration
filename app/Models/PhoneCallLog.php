<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneCallLog extends Model
{
    use HasFactory;

    protected $table = 'frontdesk_phone_call_logs';

    protected $fillable = [
        'name',
        'phone',
        'date',
        'description',
        'next_follow_up_date',
        'call_duration',
        'note',
        'call_type'
    ];

    protected $casts = [
        'date' => 'date',
        'next_follow_up_date' => 'date'
    ];
}