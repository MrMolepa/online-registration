<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FunWalkPayment extends Model
{
    use HasFactory;

    protected $table = 'fun_walk_payments';

    protected $fillable = [
        'registration_id',
        'amount',
        'payment_method',
        'transaction_ref',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function registration()
    {
        return $this->belongsTo(FunWalkRegistration::class, 'registration_id');
    }
}
