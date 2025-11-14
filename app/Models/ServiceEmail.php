<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceEmail extends Model
{
    use HasFactory;

    protected $table = "one_time_service_emails";

    protected $fillable = [
        'one_time_service_id',
         'email'
    ];


    public function one_time_service()
    {
        return $this->belongsTo(OneTimeService::class, 'one_time_service_id', 'id');
    }


}
