<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Candidate extends Model
{
    use HasFactory,
        LogsActivity;




    protected $table = "candidates";
    protected $primaryKey = 'candidate_no';
  

    protected $fillable = [
        'national_id',
        'candidate_no',
        'candidate_surname',
        'candidate_other_name',
        'date_of_birth',
        'gender',
    ];

    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    protected static $recordEvents = ['created', 'updated', 'deleted'];
    protected static $logAttributes = [
        'candidate_no',
        'candidate_surname',
        'candidate_other_name',
        'date_of_birth',
        'gender',
    ];
}
