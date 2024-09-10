<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $table = "levels";

    protected $fillable = [
        'level',
        'description',
        'is_active',
        'private_registration',
    ];




    public function fees()
    {
        return $this->belongsToMany(Subject::class, 'fee_level', 'level_id', 'fee_id');
    }



    public function centers()
    {
        return $this->belongsToMany(Center::class, 'center_level', 'level_id', 'center_id');
    }
}
