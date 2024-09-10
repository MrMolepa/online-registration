<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OneTimeServicesItem extends Model
{
    use HasFactory;

    protected $table = "one_time_services_item";

    protected $fillable = [
        'one_time_services_id',
        'name',
        'description',
        'financial_year',
        'price'
    ];

    public function oneTimeService()
    {
        return $this->belongsTo(OneTimeService::class,'one_time_services_id','id');
    }
}
