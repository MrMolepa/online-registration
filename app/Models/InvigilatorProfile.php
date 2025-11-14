<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvigilatorProfile extends Model
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
        'mpesa_phone_number',
        'ecocash_phone_number',
        'tin_number',
        'progress_status_id',
        'experience_id',
        'accessibility',
        'integrity',
        'principal_declare',
        'workshop',
        'session',
        'financial_year',
        'district_id',
        'village',
    ];
    public function timesheet()
    {
        return $this->belongsToMany( Timetable::class, 'invigilation_timesheet','profile_id', 'timetable_id')->withPivot('id');
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
    public function invigilator_district()
    {
        return $this->belongsTo(District::class, 'district_id', 'district_code');
    }
    public function invigilator_payment()
    {
        return $this->belongsTo(InvigilationPaymentMethod::class, 'payment_id', 'id');
    }
}
