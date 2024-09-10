<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amendment extends Model
{
    use HasFactory;
    protected $table = "amendments";
    public $timestamps = false;

    protected $fillable = [
        'candidate_no',
        'candidate_surname',
        'candidate_other_name',
        'date_of_birth',
        'gender',
        'amend_date'
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
        'amend_date'
    ];
}
