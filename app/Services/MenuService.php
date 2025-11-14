<?php

namespace App\Services;

use App\Models\Menu;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class MenuService
{
    /**
     * Get menus accessible to the current authenticated user.
     *
     * @param  string|null  $guard
     * @param  int|null  $parentId
     * @return \Illuminate\Support\Collection
     */
    public function getMenus(?string $guard = null, ?int $parentId = null): Collection
    {
        $user = Auth::guard($guard)->user();

        return Menu::where('is_active', true)
            ->where('parent_id', $parentId)
            ->orderBy('order')
            ->get()
            ->filter(function ($menu) use ($user) {
                // Only show menus the user has permission to see
                return !$menu->permission || ($user && $user->can($menu->permission));
            });
    }

    /**
     * Recursively render a menu tree into HTML.
     *
     * @param  string|null  $guard
     * @param  int|null  $parentId
     * @return string
     */
    public function renderMenu(?string $guard = null, ?int $parentId = null): string
    {
        $menus = $this->getMenus($guard, $parentId);

        $html = '';

        foreach ($menus as $menu) {
            $children = $this->getMenus($guard, $menu->id);

            if ($children->count() > 0) {
                $html .= '
                    <li class="nav-item">
                        <a href="#submenu-' . $menu->id . '" data-toggle="collapse" class="nav-link collapsed">
                            <i class="' . e($menu->icon) . '"></i>
                            <span class="menu-title">' . e($menu->name) . '</span>
                            <i class="mdi mdi-chevron-left menu-arrow"></i>
                        </a>
                        <div id="submenu-' . $menu->id . '" class="collapse">
                            <ul class="nav flex-column sub-menu">
                                ' . $this->renderMenu($guard, $menu->id) . '
                            </ul>
                        </div>
                    </li>
                ';
            } else {
                $route = $menu->route ? route($menu->route) : '#';
                $activeClass = request()->routeIs($menu->route) ? 'active-menu' : '';
                $html .= '
                    <li class="nav-item">
                        <a href="' . $route . '" class="nav-link ' . $activeClass . '">
                            <i class="' . e($menu->icon) . '"></i>
                            <span class="menu-title">' . e($menu->name) . '</span>
                        </a>
                    </li>
                ';
            }
        }

        return $html;
    }
}