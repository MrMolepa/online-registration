<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidatePaymentConfirmation extends Model
{
    use HasFactory;

    protected $table = "candidate_confirmation";

    protected $fillable = [
        'candidate_no',
        'candidate_info',
        'bank_ref',
        'bank_confirmation',
        'bank_confirmation_path',
        'comments',
        'amount',
        'checked_status',
        'checked_by'
    ];

    public function candidate()
    {
        return $this->belongsTo(CenterCandidate::class, 'candidate_no','candidate_no');
    }
}
