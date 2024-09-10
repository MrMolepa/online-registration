<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $table = "activity";

    protected $fillable = [
        'process',
        'activity_type',
        'name',
        'description',
    ];




    public function activityType()
    {
        return $this->belongsTo(ActivityType::class, 'activity_type', 'id');
    }


    public function transitions()
    {
        return $this->belongsToMany(Transition::class, 'transition_activity', 'activity_id', 'transition_id');
    }
}
