<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvigilationCatergories extends Model
{
    use HasFactory;
    use HasFactory;
    protected $table = 'invigilation_catergories';
    protected $fillable = [
        'name',
        'description',
    ];
}
