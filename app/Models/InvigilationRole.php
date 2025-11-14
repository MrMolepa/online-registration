<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvigilationRole extends Model
{
    use HasFactory;
    protected $table = 'invigilation_roles';
    protected $fillable = [
        'invigilation_type_id',
        'invigilation_candidate_id',
        'invigilator_number',
        'invigilator_paymentamount_id',
        'is_sessions',
    ];

    public function invigilation_type()
    {
        return $this->belongsTo(InvigilationType::class, 'invigilation_type_id', 'id');
    }
    public function invigilation_candidate()
    {
        return $this->belongsTo(InvigilationCandidate::class, 'invigilation_candidate_id', 'id');
    }
    public function invigilator_paymentamount()
    {
        return $this->belongsTo(InvigilatorPaymentAmount::class, 'invigilator_paymentamount_id', 'id');
    }
}
