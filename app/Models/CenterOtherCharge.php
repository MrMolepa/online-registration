<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CenterOtherCharge extends Model
{
    use HasFactory;

    protected $table = "center_other_charges";


    protected $fillable = [
        'center_no',
        'amount',
        'remarks',
        'collected_by',
        'session',
        'financial_year',
    ];

    public function center()
    {
        return $this->belongsTo(Center::class, 'center_no');
    }


}
