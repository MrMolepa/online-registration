<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    use HasFactory;

    protected $table = "components";

    protected $fillable = [
        'subject_code',
        'component_code',
        'component_name'
    ];

    // ==================== Existing Relationships ====================
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_code');
    }

    // ==================== Stationery Management Relationships ====================
    
    /**
     * Get all component stocks (allocation rules) for this component
     */
    public function componentStocks()
    {
        return $this->hasMany(ComponentStock::class, 'component_id');
    }

    /**
     * Get stock items linked to this component through allocation rules
     */
    public function stockItems()
    {
        return $this->belongsToMany(StockItem::class, 'stationery_component_stock', 'component_id', 'stock_item_id')
            ->withPivot([
                'rule_type',
                'base_quantity',
                'multiplier',
                'extras_fixed',
                'extras_percent',
                'extras_per_candidate',
                'extras_percent_candidates',
                'extras_condition',
                'condition_value'
            ]);
    }

    /**
     * Get center components (candidate assignments) for this component
     */
    public function centerComponents()
    {
        return $this->hasMany(CenterComponent::class, 'component_id');
    }

    /**
     * Get center stock allocations for this component
     */
    public function centerStocks()
    {
        return $this->hasMany(CenterStock::class, 'component_id');
    }

    /**
     * Calculate total stock requirements for this component across all centers
     */
    public function calculateTotalRequirements()
    {
        $requirements = [];
        
        // Get all centers with candidate numbers for this component
        $centerComponents = $this->centerComponents()->with('center')->get();
        
        // Get all allocation rules for this component
        $allocationRules = $this->componentStocks()->with('stockItem')->get();
        
        foreach ($allocationRules as $rule) {
            $stockItemId = $rule->stock_item_id;
            $totalQty = 0;
            
            foreach ($centerComponents as $centerComponent) {
                $qty = $rule->calculateQuantity(
                    $centerComponent->number_of_candidates,
                    1 // per center
                );
                $totalQty += $qty;
            }
            
            $requirements[$stockItemId] = [
                'stock_item' => $rule->stockItem,
                'total_required' => $totalQty,
                'centers_count' => $centerComponents->count()
            ];
        }
        
        return $requirements;
    }
}