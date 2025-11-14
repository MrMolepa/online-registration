<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    use HasFactory;
    protected $table = "sms_templates";

    protected $fillable = ['name', 'description', 'content', 'user_id'];

    public function user()
    {
        return $this->belongsTo(AdminUser::class);
    }
}
