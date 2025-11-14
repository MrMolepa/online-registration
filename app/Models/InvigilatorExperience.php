<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvigilatorExperience extends Model
{
    use HasFactory;
    use HasFactory;
    protected $table = 'invigilator_experience';
    protected $fillable = [
        'years',
    ];
}
