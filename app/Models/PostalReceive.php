<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostalReceive extends Model
{
    use HasFactory;

    protected $table = 'frontdesk_postal_receives';

    protected $fillable = [
        'package_name',
        'from_title',
        'to_title',
        'reference_number',
        'date_received',
        'created_by'
    ];

    protected $casts = [
        'date_received' => 'date'
    ];
}