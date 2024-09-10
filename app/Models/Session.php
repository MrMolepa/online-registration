<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $table = "sessions";

    protected $fillable = [
        'session',
        'description',
        'financial_year',
        'financial_closing_date',
        'is_active'
    ];
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'session_subject','subject_code','session_id',);
    }



}
