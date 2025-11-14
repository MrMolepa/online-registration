<?php

namespace App\Helpers;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;


use App\Models\Menu;

class MenuHelper
{
    public static function getAllMenus($guard = null): Collection
    {
       

        // Load all active menus once
        return Menu::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->filter(function ($menu) use ($user) {
                // Check permissions
                return !$menu->permission || $user->can($menu->permission);
            });
    }

    public static function renderMenu($menus)
    {
        $html = '';

        foreach ($menus as $menu) {
            $children = self::getAllMenus($menu->id);

            if ($children->count() > 0) {
                $html .= '
                    <li>
                        <a href="#submenu-' . $menu->id . '" data-toggle="collapse" class="collapsed">
                            <i class="' . e($menu->icon) . '"></i>
                            <span>' . e($menu->name) . '</span>
                            <i class="icon-submenu lnr lnr-chevron-left"></i>
                        </a>
                        <div id="submenu-' . $menu->id . '" class="collapse">
                            <ul class="nav">
                                ' . self::renderMenu($children) . '
                            </ul>
                        </div>
                    </li>
                ';
            } else {
                $route = $menu->route ? route($menu->route) : '#';
                $html .= '
                    <li>
                        <a href="' . $route . '">
                            <i class="' . e($menu->icon) . '"></i>
                            <span>' . e($menu->name) . '</span>
                        </a>
                    </li>
                ';
            }
        }

        return $html;
    }
}