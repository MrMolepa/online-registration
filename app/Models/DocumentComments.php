<?php

namespace App\Models;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Builder;

class DocumentComments extends Model
{
    use HasFactory;

    protected  $table = 'document_comments';


    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'modified_date';

    protected $fillable = [
        'document_id', 'comment', 'created_by',
        'modified_by', 'is_deleted'
    ];

    public function user()
    {
        return $this->belongsTo(DocumentUser::class, 'user_id');
    }



    public function document()
    {
        return $this->belongsTo(Documents::class, 'document_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            $userId = Auth::user()->document_user_profile->id;
            $model->created_by= $userId;
            $model->modified_by =$userId;
        });
        static::updating(function (Model $model) {
            $userId =Auth::user()->document_user_profile->id;
            $model->modified_by =$userId;
        });


    }
}
