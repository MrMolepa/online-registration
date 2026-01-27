<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CenterStock extends Model
{
    use HasFactory;

    protected $table = 'stationery_center_stock';

    protected $fillable = [
        'center_no',
        'stock_item_id',
        'component_id',
        'session_id',
        'num_candidates',
        'quantity_allocated',
        'quantity_dispatched',
        'dispatch_date',
        'received_date',
        'allocation_breakdown',
        'status',
        'notes'
    ];

    protected $casts = [
        'quantity_allocated' => 'decimal:2',
        'quantity_dispatched' => 'decimal:2',
        'dispatch_date' => 'date',
        'received_date' => 'date',
        'allocation_breakdown' => 'array',
        'num_candidates' => 'integer',
    ];

    /**
     * Relationship to Center
     */
    public function center()
    {
        return $this->belongsTo(Center::class, 'center_no');
    }

    /**
     * Relationship to Stock Item
     */
    public function stockItem()
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id');
    }

    /**
     * Relationship to Component
     */
    public function component()
    {
        return $this->belongsTo(Component::class, 'component_id');
    }

    /**
     * Relationship to Session
     */
    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    /**
     * Mark allocation as dispatched
     */
    public function markDispatched($dispatchedQty = null)
    {
        $this->quantity_dispatched = $dispatchedQty ?? $this->quantity_allocated;
        $this->dispatch_date = now();
        $this->status = 'dispatched';
        $this->save();
    }

    /**
     * Mark allocation as received
     */
    public function markReceived()
    {
        $this->received_date = now();
        $this->status = 'received';
        $this->save();
    }
}