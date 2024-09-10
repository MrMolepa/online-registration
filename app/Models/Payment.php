<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Payment extends Model
{
    use HasFactory, LogsActivity;

    protected $table = "payments";

    protected $fillable = [
        'invoice_id',
        'reference_no',
        'amount',
    ];


    protected static $recordEvents = ['created', 'updated', 'deleted'];

    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;


    protected static $logAttributes = [
        'invoice_id',
        'reference_no',
        'amount',
    ];
}
