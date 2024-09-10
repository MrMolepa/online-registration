<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OneTimeService extends Model
{
    use HasFactory;

    protected $table = "one_time_services";

    protected $fillable = [
        'name',
        'desciption'
    ];


    public function OneTimeServicesItem()
    {
        return $this->hasMany(OneTimeServicesItem::class);
    }
}
