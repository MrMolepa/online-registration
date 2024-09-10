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
        'branch',
        'account_number',
        'payable_phone_number',
        'tin_number'
    ];
}
