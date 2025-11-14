<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitationRecipient extends Model
{
    use HasFactory;

    protected $table = 'invitation_recipients';

    protected $fillable = [
        'national_id',
        'first_name',
        'last_name',
        'center_no',
        'email',
        'phone_number',
        'village',
        'district',
        'type',
    ];


    public function center()
    {
        return $this->belongsTo(Center::class, 'center_no');
    }

    public function Invitations()
    {
        return $this->hasMany(Invitation::class, 'recipient_id', 'id');
    }


    /**
     * Invitation can also directly access recipient fields.
     */



    public function recipientFields()
    {
        return $this->hasMany(InvitationRecipientField::class, 'recipient_id', 'id');
    }
}
