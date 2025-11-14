<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitationPayment extends Model
{
    use HasFactory;

    protected $table = 'invitation_payments';


    protected $fillable = [
        'role_id',
        'payment_id',
        'invitation_id',
        'bank_name',
        'branch',
        'account_number',
        'payable_phone_number',
        'tin_number',
    ];




    public function invitation()
    {
        return $this->belongsTo(Invitation::class, 'invitation_id');
    }
}
