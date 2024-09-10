<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvigilationCandidate extends Model
{
    use HasFactory;
    use HasFactory;
    protected $table = 'invigilation_candidates';
    protected $fillable = [
        'invigilation_type_id',
        'range_start',
        'range_end',
    ];
}
