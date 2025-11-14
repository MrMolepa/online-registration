<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitationRole extends Model
{
    use HasFactory;

    protected $table = 'invitation_roles';

    protected $fillable = [
        'name',
        'description',
        'type',
        'contract_template'
    ];


      public function invitations()
    {
        return $this->hasMany(Invitation::class, 'role_id', 'id');
    }

    /**
     * A Role has many dynamic fields.
     */
    public function fields()
    {
        return $this->hasMany(InvitationRoleField::class, 'role_id', 'id');
    }


}
