<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceAccount extends Model
{
    use HasFactory;
    protected $table = "finance_accounts";
    protected $fillable = [
        'name',
        'description',
        'account_number',
        'balance',
        'created_at',
        'updated_at'

    ];
}
