<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    use HasFactory;

    protected $table = 'frontdesk_enquiries';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'enquiry_date',
        'description',
        'is_active'
    ];

    protected $casts = [
        'enquiry_date' => 'date',
        'is_active' => 'boolean'
    ];
}