<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllocationScenario extends Model
{
    use HasFactory;

    protected $table = 'allocation_scenarios';

    protected $fillable = [
        'code',
        'name',
        'description',
        'condition_type', // 'candidate_range', 'component_type', 'center_location', 'custom'
        'condition_min',
        'condition_max',
        'condition_attribute', // e.g., 'is_rural', 'component_type'
        'condition_value', // e.g., 'true', 'consumable'
        'use_multiplier',
        'use_fixed_extras',
        'use_per_candidate_extras',
        'use_percentage_extras',
        'priority', // Higher priority scenarios are checked first
        'is_active',
    ];

    protected $casts = [
        'condition_min' => 'integer',
        'condition_max' => 'integer',
        'use_multiplier' => 'boolean',
        'use_fixed_extras' => 'boolean',
        'use_per_candidate_extras' => 'boolean',
        'use_percentage_extras' => 'boolean',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Relationship to component stocks
     */
    public function componentStocks()
    {
        return $this->belongsToMany(
            ComponentStock::class,
            'component_stock_scenarios',
            'scenario_id',
            'component_stock_id'
        )->withPivot('is_primary', 'adjustment_value', 'adjustment_type');
    }

    /**
     * Check if this scenario matches the given conditions
     * 
     * @param int $numCandidates
     * @param array $attributes
     * @return bool
     */
    public function matches($numCandidates, $attributes = [])
    {
        return match($this->condition_type) {
            'candidate_range' => $this->matchesCandidateRange($numCandidates),
            'component_type' => $this->matchesAttribute($attributes, 'component_type'),
            'center_location' => $this->matchesAttribute($attributes, 'is_rural'),
            'custom' => $this->matchesCustomCondition($attributes),
            default => false,
        };
    }

    /**
     * Check if candidate count falls within range
     */
    private function matchesCandidateRange($numCandidates)
    {
        if ($this->condition_min && $numCandidates < $this->condition_min) {
            return false;
        }
        if ($this->condition_max && $numCandidates > $this->condition_max) {
            return false;
        }
        return true;
    }

    /**
     * Check if attribute matches expected value
     */
    private function matchesAttribute($attributes, $key)
    {
        if (!isset($attributes[$key])) {
            return false;
        }
        
        $attributeValue = $attributes[$key];
        $expectedValue = $this->condition_value;

        // Handle boolean values
        if ($expectedValue === 'true') {
            return $attributeValue === true || $attributeValue === 'true' || $attributeValue === 1;
        }
        if ($expectedValue === 'false') {
            return $attributeValue === false || $attributeValue === 'false' || $attributeValue === 0;
        }

        // String comparison
        return strtolower($attributeValue) === strtolower($expectedValue);
    }

    /**
     * Custom condition matching (can be extended)
     */
    private function matchesCustomCondition($attributes)
    {
        // Can implement custom logic here
        // For now, check if attribute exists and matches
        if ($this->condition_attribute && isset($attributes[$this->condition_attribute])) {
            return $this->matchesAttribute($attributes, $this->condition_attribute);
        }
        return true;
    }

    /**
     * Scope for primary scenarios
     */
    public function scopePrimary($query)
    {
        return $query->whereHas('componentStocks', function($q) {
            $q->wherePivot('is_primary', true);
        });
    }

    /**
     * Scope for add-on scenarios
     */
    public function scopeAddon($query)
    {
        return $query->whereHas('componentStocks', function($q) {
            $q->wherePivot('is_primary', false);
        });
    }
}