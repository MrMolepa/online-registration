<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CenterStock extends Model
{
    use HasFactory;

    protected $table = 'stationery_center_stock';

    protected $fillable = [
        'center_id',
        'stock_item_id',
        'component_id',
        'quantity_allocated',
        'quantity_dispatched',
        'dispatch_date',
        'received_date',
        'notes'
    ];

    protected $casts = [
        'quantity_allocated' => 'decimal:2',
        'quantity_dispatched' => 'decimal:2',
        'dispatch_date' => 'date',
        'received_date' => 'date'
    ];

    // Relationships
    public function center()
    {
        return $this->belongsTo(Center::class, 'center_id', 'center_no');
    }

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id');
    }

    public function component()
    {
        return $this->belongsTo(Component::class, 'component_id');
    }
}
