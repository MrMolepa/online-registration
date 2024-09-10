<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialNeed extends Model
{
    use HasFactory;

    protected $table = "special_need";
    protected $fillable = [
        'arrangement_id',
        'name',
    ];


}
