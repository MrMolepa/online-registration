<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostalDispatch extends Model
{
    use HasFactory;

    protected $table = 'frontdesk_postalDispatch';

    protected $fillable = [
        'to',
        'reference_no',
        'address',
        'from',
        'date',
        'created_by'
    ];

    protected $casts = [
        'date' => 'date',
    ];
}