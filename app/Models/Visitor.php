<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $table = 'frontdesk_visitors';

    protected $fillable = [
        'purpose',
        'meeting_with',
        'visitor_name',
        'phone',
        'number_of_people',
        'date',
        'in_time',
        'out_time',
        'created_by'
    ];

    protected $casts = [
        'date' => 'date',
        'in_time' => 'datetime:H:i',
        'out_time' => 'datetime:H:i',
    ];
}