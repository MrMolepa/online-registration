<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CenterCandidate extends Model
{
    use HasFactory, LogsActivity;
    protected $table = "center_candidate";
    protected $fillable = [
        'candidate_no',
        'national_id',
        'center_no',
        'type',
        'sponser',
        'phone_number',
        'email',
        'level',
        'financial_year',
        'session',
        'address',
        'subject_number',
    ];



    protected static $recordEvents = ['created', 'updated', 'deleted'];

    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;


    protected static $logAttributes = [
        'candidate_no',
        'center_no',
        'type',
        'session',
        'sponser',
        'phone_number',
        'level',
        'subject_number',
    ];




    public function center()
    {
        return $this->belongsTo(Center::class, 'center_no');
    }
}
