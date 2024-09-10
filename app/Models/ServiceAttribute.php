<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceAttribute extends Model
{
    use HasFactory;

    protected $table = "services_attributes";

    protected $fillable = [
        'name', 'code','placeholder' ,'one_time_service_id', 'frontend_type', 'is_required',
    ];
}
