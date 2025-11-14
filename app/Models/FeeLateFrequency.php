<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeLateFrequency extends Model
{
    use HasFactory;
    protected $table = "fee_late_freequencies";
    protected $fillable = [
        'name',
        'description',
        'created_at',
        'updated_at'
    ];
}
