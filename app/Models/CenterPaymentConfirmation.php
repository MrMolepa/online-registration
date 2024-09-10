<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CenterPaymentConfirmation extends Model
{
    use HasFactory;


    protected $table = "bank_statements";


    protected $fillable = [
        'center_id',
        'email',
        'phone_no',
        'bank_statement',
        'bank_statement_path',
        'amount_paid',
        'checked_by',
        'financial_year',
        'bank_ref',
        'checked_status',
        'checked_date'
    ];

    public function center()
    {

        return $this->belongsTo(Center::class, 'center_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }
}
