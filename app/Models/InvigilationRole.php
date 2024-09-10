<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvigilationRole extends Model
{
    use HasFactory;
    use HasFactory;
    protected $table = 'invigilation_roles';
    protected $fillable = [
        'invigilation_type_id',
        'invigilation_candidate_id',
        'invigilator_number',
        'amount',
    ];

    public function invigilation_type()
    {
        return $this->belongsTo(InvigilationType::class, 'invigilation_type_id', 'id');
    }
    public function invigilation_candidate()
    {
        return $this->belongsTo(InvigilationCandidate::class, 'invigilation_candidate_id', 'id');
    }
}
