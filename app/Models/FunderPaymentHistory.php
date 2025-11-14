<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FunderPaymentHistory extends Model
{
    use HasFactory;

    protected $table = 'funder_payment_histories';

    protected $fillable = [
        'sponsor',
        'email',
        'phone_no',
        'reference_no',
        'attachment',
        'pay_via',
        'amount',
        'collect_by',
        'status',
        'fine',
        'level',
        'session',
        'financial_year',
        'remarks',
    ];
}
