<?php

namespace App\Models;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Builder;

class DocumentUser extends Model
{
    use HasFactory;

    protected  $table = 'document_users';



    protected $fillable = [
        'documentUser_id',
        'documentUser_type',
    ];





    public function document_user()
    {
        return $this->morphTo();
    }





}
