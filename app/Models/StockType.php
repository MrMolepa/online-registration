<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockType extends Model
{
    use HasFactory;
    protected $table = 'stationery_stock_types';

    protected $fillable = [
        'name',
        'description',
    ];

    public function stockItems()
    {
        return $this->hasMany(StockItem::class, 'stock_type_id');
    }   
}
