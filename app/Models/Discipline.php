<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discipline extends Model
{
    use HasFactory;
    protected $table = "disciplines";

    protected $fillable = [
        'name',
        'display_name',
        'level_id'
    ];

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }
}
