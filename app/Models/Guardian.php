<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    use HasFactory;
    protected $table = "guardians";
    protected $fillable = [
        'candidate', 'guardian_type', 'national_id', 'name', 'surname', 'phone_number', 'email'
    ];
}
