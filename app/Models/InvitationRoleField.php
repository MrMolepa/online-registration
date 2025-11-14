<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitationRoleField extends Model
{
    use HasFactory;

    protected $table = 'invitation_role_fields';

    protected $fillable = [
        'role_id',
        'label',
        'name',
        'type',
        'source',
        'required',
        'options',
        'key_column',
        'value_column',
        'is_visible',
        'source_type',
    ];




    protected $casts = [
        'is_visible' => 'boolean',
        'required' => 'boolean',
        'options' => 'array', // will automatically decode JSON to array
    ];

    /**
     * A RoleField belongs to a Role.
     */
    public function role()
    {
        return $this->belongsTo(InvitationRole::class, 'role_id');
    }


    public function positions()
    {
        return $this->hasMany(InvitationFieldPosition::class, 'field_id');
    }



    public function responses()
    {
        return $this->hasMany(InvitationRecipientField::class, 'field_id');
    }
}
