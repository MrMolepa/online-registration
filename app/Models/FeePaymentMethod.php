<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeePaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'fee_payment_method';

    protected $fillable = [
        'name',
        'description',
        'Type',
        'created_at',
        'updated_at'
    ];
}
