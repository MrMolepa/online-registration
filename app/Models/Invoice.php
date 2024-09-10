<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model
{
    use HasFactory, LogsActivity;


    protected $table = "invoices";

    protected $fillable = [
        'client_id',
        'reference_no',
        'financial_year',
        'national_id',
        'level',
        'session',
        'amount',
        'service'
    ];
}
