<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectSession extends Model
{
    use HasFactory;


    protected $table = "subject_session";

    protected $fillable = [
        'subject_code','session_id',
    ];




}
