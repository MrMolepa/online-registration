<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CenterPayment extends Model
{
    use HasFactory;

    protected $table = "center_payment_histories";


    protected $fillable = [
        'center_no',
        'email',
        'phone_no',
        'reference_no',
        'attachment',
        'amount',
        'collect_by',
        'status',
        'session',
        'financial_year',
        'remarks',
    ];


    public function center()
    {
        return $this->belongsTo(Center::class, 'center_no');
    }

}
