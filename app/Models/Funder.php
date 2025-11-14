<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funder extends Model
{
    use HasFactory;

    protected $table = 'funders';

    protected $fillable = [
        'sponsor',
        'name',
        'description',
        'status'
    ];
}
