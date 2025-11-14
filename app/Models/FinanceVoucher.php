<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceVoucher extends Model
{
    use HasFactory;
    protected $table = "finance_vouchers_head";
    protected $fillable = [
        'name',
        'description',
        'type',
        'created',
        'updated_at'

    ];
}
