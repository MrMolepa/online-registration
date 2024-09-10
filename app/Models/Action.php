<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasFactory;

    protected $table = "actions";

    protected $fillable = [
        'action_type',
        'process',
        'name',
        'description',
        'order_number'
    ];

    public function actionType()
    {
        return $this->belongsTo(ActionType::class, 'action_type', 'id');
    }

    public function transitions()
    {
        return $this->belongsToMany(Transition::class, 'transition_action', 'action_id', 'transition_id');
    }

    public function users($related)
    {
        return $this->belongsToMany($related, 'action_user', 'action_id', 'user_id');
    }
}
