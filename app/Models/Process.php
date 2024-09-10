<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    use HasFactory;

    protected $table = "processes";

    protected $fillable = [
        'name',
        'initial_state',
        'description',
        'process_key'
    ];



    public function users()
    {
        return $this->belongsToMany(AdminUser::class, 'process_users', 'process_id', 'user_id');
    }
}
