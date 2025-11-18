<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    use HasFactory;
    protected $table = 'stationery_stock_items';
    protected $fillable = [
        'stock_type_id',
        'name',
        'unit',
        'stock_qty',
        'supplier_info',
        'is_active',     
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'stock_qty' => 'decimal:2',
    ];
    public function stockType()
    {
        return $this->belongsTo(StockType::class, 'stock_type_id');
    }

     public function componentStocks()
    {
        return $this->hasMany(ComponentStock::class, 'stock_item_id');
    }

    public function centerStocks()
    {
        return $this->hasMany(CenterStock::class, 'stock_item_id');
    }
}

