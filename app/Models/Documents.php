<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Builder;

class Documents extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'modified_date';
    protected $table = 'documents';


    protected $fillable = [
        'category_id',
        'name',
        'description',
        'url',
        'created_by',
        'modified_by',
        'is_deleted',
        'location',
         'is_permanent_delete'
    ];

    public function categories()
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo(DocumentUser::class, 'created_by', 'id');
    }

    public function documentMetaDatas()
    {
        return $this->hasMany(DocumentMetaDatas::class, 'document_id');
    }

    public function documentComments()
    {
        return $this->hasMany(DocumentComments::class, 'documentId');
    }

    // public function userNotifications()
    // {
    //     return $this->hasMany(UserNotifications::class, 'documentId');
    // }

    // public function reminderSchedulers()
    // {
    //     return $this->hasMany(ReminderSchedulers::class, 'documentId');
    // }

    // public function reminders()
    // {
    //     return $this->hasMany(Reminders::class, 'documentId');
    // }

    public function documentVersions()
    {
        return $this->hasMany(DocumentVersions::class, 'document_id');
    }

    public function documentUserPermissions()
    {
        return $this->hasMany(DocumentUserPermissions::class, 'document_Id');
    }
    public function documentRolePermissions()
    {
        return $this->hasMany(DocumentRolePermissions::class, 'documentId');
    }

    // public function documentAuditTrails()
    // {
    //     return $this->hasMany(DocumentAuditTrails::class, 'documentId');
    // }

    protected static function boot()
    {
        parent::boot();
        static::creating(function (Model $model) {
           $userId = Auth::user()->id;
            $model->created_by = $userId;
            $model->modified_by = $userId;
        });
        static::updating(function (Model $model) {
            $userId = Auth::user()->id;
            $model->modified_by = $userId;
        });

    }
}
