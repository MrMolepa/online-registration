<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitationRecipientField extends Model
{
    use HasFactory;

    protected $table = 'invitation_recipient_fields';

    protected $fillable = [
        'invitation_id',
        'recipient_id',
        'field_id',
        'field_key',
        'field_value'
    ];

    /**
     * Field belongs to a recipient.
     */
    public function recipient()
    {
        return $this->belongsTo(InvitationRecipient::class, 'recipient_id');
    }





    /**
     * Field belongs to an invitation.
     */
    public function invitation()
    {
        return $this->belongsTo(Invitation::class, 'invitation_id');
    }


    /**
     * (Optional) If you have a separate "Field" definitions table.
     */
    public function fieldDefinition()
    {
        return $this->belongsTo(InvitationRoleField::class, 'field_id');
    }
}
