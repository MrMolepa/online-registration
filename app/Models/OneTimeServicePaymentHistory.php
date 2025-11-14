<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OneTimeServicePaymentHistory extends Model
{
    use HasFactory;
    protected $table = "one_time_service_payment_histories";
    protected $fillable = [
        'client_id',
        'collected_by',
        'reference_no',
        'pay_via',
        'fine',
        'amount',
        'remarks',
        'attachment',
        'status',
    ];

    
}
