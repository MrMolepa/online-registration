<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class FeeStracture extends Model
{
    use HasFactory, LogsActivity;

    protected $table = "fees_stracture";

    protected $fillable = [
        'candidate_type',
        'subject_fee',
        'registration_fee',
        'local_fee',
        'practical_subject_fee',
        'level',
        'delf_fee',
        'session',
        'bank_charge',
        'financial_year',
    ];


    protected static $recordEvents = ['created', 'updated', 'deleted'];

    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;


    protected static $logAttributes = [
        'candidate_type',
        'subject_fee',
        'registration_fee',
        'level',
        'session',
        'local_fee',
        'practical_subject_fee',
        'bank_charge',
        'financial_year',
    ];

    public function selectedSession()
    {
        return $this->belongsTo(Session::class, 'session', 'id');
    }
    public function selectedLevel()
    {
        return $this->belongsTo(level::class, 'level', 'id');
    }
}
