<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateArrangement extends Model
{
    use HasFactory;

    protected $table = "candidate_arrangement";


    protected $fillable = [
        'candidate_no', 'arrangement_id'
    ];
}
