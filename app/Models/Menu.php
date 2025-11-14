<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{

    use HasFactory;
    protected $table = 'menus';

    protected $fillable = [
        'parent_id',
        'name',
        'route',
        'icon',
        'order',
        'guard_name',
        'is_active',

    ];

    protected static function boot() 
    {
        parent::boot();

        static::creating(function ($menu){
            if($menu->parent_id){
                $menu->order = Menu::where('parent_id', $menu->parent_id)->max('order') + 1;
            }else{
                $menu->order = Menu::whereNull('parent_id')->max('order') + 1;
        }
        
    });

        static::deleted(function ($menu){


            // Reorder sibling menus
            $siblings = Menu::where('parent_id', $menu->parent_id)
                ->orderBy('order')
                ->get();
            $count = 1;
            foreach ($siblings as $sibling) {
                $sibling->update(['order' => $count++]);
            }        
        
            });

    }

    // Each menu can have multiple child menus
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }
    //Each menu can belong to one parent menu
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }   

    public function permissions()
    {
        return $this->hasMany(MenuPermission::class, 'menu_id');
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];
}