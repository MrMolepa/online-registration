<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Traits\Uuids;

class DocumentRolePermissions extends Model
{
    use HasFactory;


    protected  $table = 'document_role_permissions';


    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'modified_date';

    protected $fillable = [
        'document_id', 'role_id', 'is_time_bound', 'start_date', 'end_date', 'is_allow_download', 'created_by',
        'modified_by', 'is_deleted'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function document()
    {
        return $this->belongsTo(Documents::class, 'document_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            $userId =  Auth::user()->id;
            $model->created_by = $userId;
            $model->modified_by = $userId;

        });

        static::updating(function (Model $model) {
            $userId =  Auth::user()->id;
            $model->modified_by = $userId;
        });
    }
}
