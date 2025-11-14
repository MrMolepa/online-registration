<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvigilationStatus extends Model
{
    use HasFactory;
    use HasFactory;
    protected $table = 'invigilation_status';
    protected $fillable = [
        'name',
        'description',
        'status',
        'order_status',
        'color_red',
        'color_green',
        'color_blue',
        

    ];
}
