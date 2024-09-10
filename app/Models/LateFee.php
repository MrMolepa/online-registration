<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LateFee extends Model
{
    use HasFactory;


    protected $table = "late_fees";

    protected $fillable = [
        'start_date',
        'end_date',
        'amount',
        'session',
        'financial_year',
    ];

    //
}
