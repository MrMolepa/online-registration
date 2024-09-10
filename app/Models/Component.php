<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    use HasFactory;


    protected $table = "components";

    protected $fillable = [
        'subject_code',
        'component_code',
        'component_name'
    ];


    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_code');

        
    }





}
