<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeCandidateHistory extends Model
{
    use HasFactory;
    protected $table = "fee_candidate_histories";
    protected $fillable = [
        'candidate_id',
        'reference_no',
        'amount',
        'fine',
        'fee_group_id',
        'attachment',
        'pay_via',
        'collect_by',
        'remarks',
        'status'
    ];


    public function CenterCandidate()
    {
        return $this->belongsTo(CenterCandidate::class, 'candidate_id','id');
    }


    public function feegroup()
    {
        return $this->belongsTo(FeeGroup::class, 'fee_group_id','id');
    }
}
