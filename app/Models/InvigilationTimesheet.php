<?php

namespace App\Models;

use App\Http\Controllers\admin\InvigilationListController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Schema;

class InvigilationTimesheet extends Model
{
    use HasFactory;

    protected $table = 'invigilation_timesheet';

    protected $fillable = [
        'profile_id',
        'timetable_id',
    ];



    public function invigilation_profile()
    {
        return $this->belongsTo(InvigilatorProfile::class, 'profile_id', 'id');
    }

    public function timetable()
    {
        return $this->belongsTo(Timetable::class, 'timetable_id', 'id');
    }
}
