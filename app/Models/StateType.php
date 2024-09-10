<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StateType extends Model
{
    use HasFactory;

    protected $table = "state_types";

    protected $fillable = [
        'name',
        'description'
    ];

}
