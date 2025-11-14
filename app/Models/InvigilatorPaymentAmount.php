<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvigilatorPaymentAmount extends Model
{
    use HasFactory;
    protected $table = 'invigilator_paymentamount';
    protected $fillable = [
        'amount',
    ];

}
