<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;


class DocumentCategory extends Model
{
    // use HasFactory, SoftDeletes;

    protected  $table = 'document_categories';

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'modified_date';

    protected $fillable = [
        'name',
        'description',
        'created_by',
        'parent_id',
        'modified_by',
        'is_deleted'
    ];

    protected $casts = [
        'created_date' => 'date',
    ];

    public function documents()
    {
        return $this->hasMany(Documents::class);
    }

     protected static function boot()
    {
        parent::boot();
        static::creating(function (Model $model) {
            $userId = Auth::user()->id;
            $model->created_by= $userId;
            $model->modified_by =$userId;
        });
        static::updating(function (Model $model) {
            $userId=Auth::user()->id;
            $model->modified_by =$userId;
        });
    }

    public function childs()
    {
        return $this->hasMany(DocumentCategory::class, 'parent_id', 'id');
    }
}
