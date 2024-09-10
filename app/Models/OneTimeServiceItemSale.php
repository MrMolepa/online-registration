<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OneTimeServiceItemSale extends Model
{
    use HasFactory;


    protected $table = "one_time_services_item_sale";

    protected $fillable = [
        'client_id',
        'reference_number',
        'is_checked',
        'comments',
        'price',
        'one_time_services_item_id',
        'one_time_services_id',
        'requirements',
        'financial_year'
    ];
}
