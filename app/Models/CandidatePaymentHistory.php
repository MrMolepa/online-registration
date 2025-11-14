<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidatePaymentHistory extends Model
{
    use HasFactory;


    protected $table = "candidate_payment_history";

    protected $fillable = [
        'id',
        'candidate_id',
        'fee_group_id',
        'reference_no', 'amount',
        'fine',
        'document_attachment',
        'remarks',
        'status',
        'collected_by',
    ];




    public function candidate()
    {
        return $this->belongsTo(CenterCandidate::class, 'candidate_id','id');
    }










}
