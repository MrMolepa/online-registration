<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CenterOtherCharge extends Model
{
    use HasFactory;

    protected $table = "center_other_charges";


    protected $fillable = [
        'center_id',
        'charge',
        'comments',
        'added_by',
        'financial_year'
    ];

    public function center()
    {
        return $this->belongsTo(Center::class, 'center_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    
}
