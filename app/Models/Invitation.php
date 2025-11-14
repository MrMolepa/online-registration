<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $table = 'invitations';

    protected $fillable = [
        'role_id',
        'recipient_id',
        'session',
        'financial_year',
        'level',
        'center_no',
        'response_token',
        'responded_at',
        'signed_document',
        'type',
        'sent_at',
        'start_date',
        'end_date',
    ];


    public function workflowInstance()
    {
        return $this->morphOne(WorkflowInstance::class, 'entity');
    }




    /**
     * All recipients linked to this invitation
     */
    public function recipients()
    {
        return $this->hasMany(InvitationRecipient::class, 'invitation_id');
    }

    public function recipient()
    {
        return $this->belongsTo(InvitationRecipient::class, 'recipient_id');
    }

    public function role()
    {
        return $this->belongsTo(InvitationRole::class, 'role_id');
    }

    // One user can have many posts
    public function invitationFields()
    {
        return $this->hasMany(InvitationRecipientField::class, 'invitation_id', 'id');
    }


    // One user can have many posts
    public function payment()
    {
        return $this->hasOne(InvitationPayment::class, 'invitation_id', 'id');
    }

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];
}
