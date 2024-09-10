<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transition extends Model
{
    use HasFactory;

    protected $table = "transitions";

    protected $fillable = [
        'process',
        'currentState',
        'nextState'
    ];

    public function selectCurrentState()
    {
        return $this->belongsTo(State::class, 'currentState', 'id');
    }

    public function selectNextState()
    {
        return $this->belongsTo(State::class, 'nextState', 'id');
    }



}
