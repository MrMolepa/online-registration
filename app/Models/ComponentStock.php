<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComponentStock extends Model
{
    use HasFactory;

    protected $table = 'stationery_component_stocks';

    protected $fillable = [
        'component_id',
        'stock_item_id',
        'base_qty',
        'multiplier',
        'extras_fixed',
        'extras_per_candidate',
        'extras_percentage',
        'rule_type',
        'is_active',
    ];

    protected $casts = [
        'base_qty' => 'decimal:2',
        'multiplier' => 'decimal:4',
        'extras_fixed' => 'integer',
        'extras_per_candidate' => 'decimal:4',
        'extras_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship to Component
     */
    public function component()
    {
        return $this->belongsTo(Component::class, 'component_id');
    }

    /**
     * Relationship to Stock Item
     */
    public function stockItem()
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id');
    }

    /**
     * Calculate allocation quantity for a center
     * 
     * @param int $numCandidates
     * @param int $numInvigilators
     * @param array $centerAttributes (e.g., is_rural, component_type)
     * @return array ['quantity' => int, 'breakdown' => array]
     */
    public function calculateAllocation($numCandidates, $numInvigilators = 0, $centerAttributes = [])
    {
        $breakdown = [];
        
        // Step 1: Determine base count based on rule type
        $baseCount = match($this->rule_type) {
            'per_candidate' => $numCandidates,
            'per_invigilator' => $numInvigilators,
            'per_center' => 1,
            'fixed' => 1,
            'conditional' => $numCandidates, // Simplified, ignores condition for now
            default => $numCandidates,
        };

        // Step 2: Calculate base amount
        $quantity = $this->calculateBaseFormula($baseCount, $breakdown);

        return [
            'quantity' => (int) ceil($quantity),
            'breakdown' => $breakdown,
            'scenarios_applied' => []
        ];
    }

    /**
     * Calculate using the base formula without scenarios
     */
    private function calculateBaseFormula($baseCount, &$breakdown)
    {
        // Step 1: Base Amount
        $baseAmount = $this->base_qty * $baseCount;
        if ($this->multiplier) {
            $baseAmount *= $this->multiplier;
        }
        $baseAmount = ceil($baseAmount);
        $breakdown['step_1_base_amount'] = $baseAmount;

        // Step 2: Add Fixed Extras
        $subtotal = $baseAmount;
        if ($this->extras_fixed) {
            $subtotal += $this->extras_fixed;
        }
        $breakdown['step_2_after_fixed'] = $subtotal;

        // Step 3: Add Per-Candidate Extras
        if ($this->extras_per_candidate) {
            $perCandidateAmount = ceil($baseCount * $this->extras_per_candidate);
            $subtotal += $perCandidateAmount;
        }
        $breakdown['step_3_after_per_candidate'] = $subtotal;

        // Step 4: Add Percentage Extras
        if ($this->extras_percentage) {
            $percentageAmount = ceil($baseAmount * ($this->extras_percentage / 100));
            $subtotal += $percentageAmount;
        }
        $breakdown['step_4_final'] = $subtotal;

        return $subtotal;
    }
}