<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Subject extends Model
{
    use HasFactory, LogsActivity;

    protected $table = "subjects";
    protected $primaryKey = 'subject_code';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'subject_code',
        'subject_name',
        'short_name',
        'discipline',
        'level',
        'is_practical',
        'is_delf',
    ];




    public function sessions()
    {
        return $this->belongsToMany(Session::class, 'session_subject','subject_code','session_id');
    }

    public function options()
    {
        return $this->belongsToMany(OptionHeader::class, 'subject_option','subject_code','option_code');
    }


    public function centers()
    {
        return $this->belongsToMany(Center::class, 'valid_center_subject','subject_code','center_no');
    }






    public function selectedLevel()
    {
        return $this->belongsTo(Level::class, 'level');
    }


    public function selectedDiscipline()
    {
        return $this->belongsTo(Discipline::class, 'discipline');
    }




    public function components()
    {
        return $this->hasMany(Component::class, 'subject_code', 'subject_code');
    }



    protected static $recordEvents = ['created', 'updated', 'deleted'];

    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    protected static $logAttributes = [
        'subject_code',
        'subject_name',
        'short_name',
        'session',
        'level'
    ];
}
