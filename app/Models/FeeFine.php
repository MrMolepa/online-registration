<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeFine extends Model
{
    use HasFactory;
    protected $table = "fee_fine";
    protected $fillable = [
        'fee_group_id',
        'fee_type_id',
        'fine_type',
        'fine_value',
        'fee_frequency_id',
        'start_date',
        'end_date',
        'created_at',
        'updated_at'
    ];
    public function feegroups()
    {
        return $this->belongsTo(FeeGroup::class, 'fee_group_id', 'id');
    }
    public function feetypes()
    {
        return $this->belongsTo(FeeType::class, 'fee_type_id', 'id');
    }
    public function frequencies()
    {
        return $this->belongsTo(FeeLateFrequency::class, 'fee_frequency_id', 'id');
    }

}
