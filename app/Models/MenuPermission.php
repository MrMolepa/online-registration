<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuPermission extends Model
{
    use HasFactory;
    protected $fillable = [
        'menu_id',
        'role_id',
        'permission_id',
        'guard_name',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class,'menu_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class,'role_id');
    }
    public function permission()
    {
        return $this->belongsTo(Permission::class,'permission_id');
    }

}