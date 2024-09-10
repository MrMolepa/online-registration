<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionHeader extends Model
{
    use HasFactory;

    protected $table = "option_heads";
     protected $primaryKey = 'option_code';
     protected $keyType = 'string';
     public $incrementing = false;

    protected $fillable = [
        'option_code',
        'alternative_option_code',
        'description',
    ];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_option','option_code','subject_code');
    }


    
}
