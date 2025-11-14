<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipients',
        'message',
        'is_unicode',
        'scheduled_at',
        'status'
    ];

    protected $casts = [
        'recipients' => 'array',
        'scheduled_at' => 'datetime'
    ];
}
