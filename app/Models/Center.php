<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Center extends Model
{
    use HasFactory;

    protected $table = "centers";
    protected $primaryKey = 'center_no';
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'center_no',
        'center_full_name',
        'center_name',
        'district',
        'district_code',
        'address',
        'level',
        'sessions',
        'district_address',
        'category_id'
    ];

    // ==================== Existing Relationships ====================
    
    public function candidates()
    {
        return $this->hasMany(CenterCandidate::class, 'center_no', 'center_no');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'valid_center_subject', 'center_no', 'subject_code');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'center_no', 'center_no');
    }

    public function bankStatements()
    {
        return $this->has(BankStatement::class);
    }

    public function otherCharge()
    {
        return $this->has(CenterOtherCharge::class);
    }

    public function levels()
    {
        return $this->belongsToMany(Level::class, 'center_level', 'center_id', 'level_id');
    }

    public function principal()
    {
        return $this->hasOne(InvitationRecipient::class, 'center_no')->where('type', 'principal');
    }

    // ==================== Stationery Management Relationships ====================
    
    /**
     * Get center stock allocations
     */
    public function centerStocks()
    {
        return $this->hasMany(CenterStock::class, 'center_id', 'center_no');
    }

    /**
     * Get center components (candidate assignments)
     */
    public function centerComponents()
    {
        return $this->hasMany(CenterComponent::class, 'center_id', 'center_no');
    }

    /**
     * Get components assigned to this center with candidate counts
     */
    public function assignedComponents()
    {
        return $this->belongsToMany(Component::class, 'stationery_center_components', 'center_id', 'component_id')
            ->withPivot('number_of_candidates');
    }

    /**
     * Calculate total stationery requirements for this center
     * 
     * @return array
     */
    public function calculateStationeryRequirements()
    {
        $requirements = [];
        
        // Get all components assigned to this center
        $centerComponents = $this->centerComponents()->with('component.componentStocks.stockItem')->get();
        
        foreach ($centerComponents as $centerComponent) {
            $component = $centerComponent->component;
            $candidates = $centerComponent->number_of_candidates;
            
            // Get allocation rules for this component
            $allocationRules = $component->componentStocks;
            
            foreach ($allocationRules as $rule) {
                $stockItemId = $rule->stock_item_id;
                
                // Calculate quantity based on rule
                $qty = $rule->calculateQuantity($candidates, 1);
                
                // Aggregate by stock item
                if (!isset($requirements[$stockItemId])) {
                    $requirements[$stockItemId] = [
                        'stock_item' => $rule->stockItem,
                        'components' => [],
                        'total_quantity' => 0
                    ];
                }
                
                $requirements[$stockItemId]['components'][] = [
                    'component' => $component,
                    'candidates' => $candidates,
                    'quantity' => $qty,
                    'rule' => $rule
                ];
                
                $requirements[$stockItemId]['total_quantity'] += $qty;
            }
        }
        
        return $requirements;
    }

    /**
     * Get allocation summary for this center
     * 
     * @return array
     */
    public function getAllocationSummary()
    {
        $requirements = $this->calculateStationeryRequirements();
        $allocations = $this->centerStocks()->with('stockItem')->get();
        
        $summary = [];
        
        foreach ($requirements as $stockItemId => $requirement) {
            $allocation = $allocations->where('stock_item_id', $stockItemId)->first();
            
            $summary[] = [
                'stock_item' => $requirement['stock_item'],
                'required' => $requirement['total_quantity'],
                'allocated' => $allocation ? $allocation->quantity_allocated : 0,
                'dispatched' => $allocation ? $allocation->quantity_dispatched : 0,
                'dispatch_date' => $allocation ? $allocation->dispatch_date : null,
                'status' => $this->getAllocationStatus($requirement['total_quantity'], $allocation)
            ];
        }
        
        return $summary;
    }

    /**
     * Determine allocation status
     * 
     * @param float $required
     * @param CenterStock|null $allocation
     * @return string
     */
    private function getAllocationStatus($required, $allocation)
    {
        if (!$allocation) {
            return 'pending';
        }
        
        if ($allocation->quantity_dispatched >= $required) {
            return 'dispatched';
        }
        
        if ($allocation->quantity_allocated >= $required) {
            return 'allocated';
        }
        
        return 'partial';
    }

    /**
     * Check if center is in a rural area (for scenario-based logic)
     * 
     * @return bool
     */
    public function isRural()
    {
        // You can customize this logic based on your district codes or categories
        $ruralDistricts = ['QN', 'QS', 'TT', 'MK', 'MM']; // Example district codes
        return in_array($this->district_code, $ruralDistricts);
    }

}