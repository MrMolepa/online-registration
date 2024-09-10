<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class SubjectCandidate extends Model
{

    use HasFactory,
        LogsActivity;

    protected $table = "candidate_subject";

    protected $fillable = [
        'candidate_no',
        'national_id',
        'subject_code',
        'session',
        'level',
        'subject_option',
        'financial_year'
    ];

    protected static $recordEvents = ['created', 'updated', 'deleted'];

    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;


    protected static $logAttributes = [
        'candidate_no',
        'subject_code',
        'session',
        'level',
        'subject_option',
    ];
}
