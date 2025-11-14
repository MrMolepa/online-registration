<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitationFieldPosition extends Model
{
    use HasFactory;

    protected $table = 'invitation_field_positions';

    protected $fillable = [
        'field_id',
        'page',
        'pos_x',
        'pos_y',
        'width',
        'height'
    ];

    public function field()
    {
        return $this->belongsTo(InvitationRoleField::class, 'field_id');
    }
}
