<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentTokens extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = "id";
    protected  $table = 'document_tokens';

    protected $fillable = [
        'document_id', 'token','created_date'
    ];

    protected $dates = ['created_date'];

    protected static function boot()
    {
        parent::boot();

        

    }
}
