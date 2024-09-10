<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arrangement extends Model
{
    use HasFactory;

    protected $table = "arrangements";
    protected $fillable = [
        'name',
        'description',
    ];
}
