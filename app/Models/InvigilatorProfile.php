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
        'branch',
        'account_number',
        'mpesa_phone_number',
        'ecocash_phone_number',
        'tin_number',
    ];
    public function invigilation_role()
    {
        return $this->belongsTo(InvigilationRole::class, 'invigilation_role_id', 'id');
    }
}
