<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeePaymentHistory extends Model
{
    use HasFactory;

    protected $table = 'fee_candidate_histories';

    protected $fillable = [
        'reference_no',
        'amount',
        'fine',
        'fee_group_id',
        'pay_via',
        'collect_by',
        'remarks',
        'status',
        'created_at',
        'updated_at'
    ];
}
