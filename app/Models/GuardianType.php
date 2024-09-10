<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuardianType extends Model
{
    use HasFactory;

    protected $table = "guardian_type";
    protected $fillable = [
        'name',
        'description',
    ];
}
