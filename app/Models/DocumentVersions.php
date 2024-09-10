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

class DocumentVersions extends Model
{
    use HasFactory, SoftDeletes;
    use Notifiable;
    const CREATED_AT = 'createdDate';
    const UPDATED_AT = 'modifiedDate';
    protected  $table = 'documentVersions';
    public $incrementing = false;

    protected $fillable = [
        'document_id', 'url', 'created_by',
        'modified_by', 'is_deleted', 'location'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function documents()
    {
        return $this->belongsTo(Documents::class, 'document_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            $userId = Auth::user()->id;;
            $model->createdBy = $userId;
            $model->modifiedBy = $userId;
        });
        static::updating(function (Model $model) {
            $userId =Auth::user()->id;;
            $model->modifiedBy = $userId;
        });


    }
}
