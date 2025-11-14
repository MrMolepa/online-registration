<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $table = "attendances";
    protected $fillable = [
        'profile_id',
        'date',
        'day',
        'time_in',
        'time_out',
    ];

    public function Invigilator()
    {
        return $this->belongsTo(InvigilatorProfile::class, 'profile_id');
    }


}
