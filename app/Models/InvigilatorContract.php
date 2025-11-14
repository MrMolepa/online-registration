<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvigilatorContract extends Model
{
    use HasFactory;
    protected $table = 'invigilator_profile';
    protected $fillable = [
        'invigilation_role_id',
        'national_id',
        'surname',
        'other_names',
        'gender',
        'date_of_birth',
        'qualification',
        'email',
        'phone_number',
        'token',
        'center_no',
        'payment_id',
        'bank_name',
        'branch',
        'account_number',
        'payable_phone_number',
        'tin_number',
        'progress_status_id',
    ];

    public function status()
    {
        return $this->belongsTo(InvigilationStatus::class, 'progress_status_id', 'id');
    }
    public function invigilation_role()
    {
        return $this->belongsTo(InvigilationRole::class, 'invigilation_role_id', 'id');
    }
    public function invigilation_status()
    {
        return $this->belongsTo(InvigilationStatus::class, 'progress_status_id', 'id');
    }
    public function invigilator_experience()
    {
        return $this->belongsTo(InvigilatorExperience::class, 'experience_id', 'id');
    }
}
