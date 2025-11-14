<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvigilationPaymentMethod extends Model
{
    use HasFactory;
    use HasFactory;
    protected $table = 'invigilation_paymentmethod';
    protected $fillable = [
        'name',
        'description',
        'is_bank_name',
        'is_account_number',
        'is_branch',
        'is_payable_phone_number',
        'is_tin_number',


    ];
}
