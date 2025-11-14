<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvigilationProprietor extends Model
{
    use HasFactory;

    protected $table = 'invigilation_proprietor';
    protected $fillable = [
        'proprietor_source',
        'proprietor_target',
    ];




}
