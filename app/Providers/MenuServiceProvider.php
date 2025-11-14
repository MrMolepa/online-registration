<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share menu data with sidebar view
        View::composer('admin.partials.sidebar', function ($view) {
            $menus = $this->getAuthorizedMenus();// get menus authorized for the current user
            $view->with('dynamicMenus', $menus);// share menus with the view   
        });
    }

    /**
     * Get menus authorized for the current user
     */
    private function getAuthorizedMenus()//get menus based on user roles and permissions
    {
        if (!Auth::check()) {
            return collect([]);// return empty collection if user is not authorized
        }

        $user = Auth::user();

        $guardName = Auth::getDefaultDriver();// get current guard name

        

        // Get all active parent menus
        $menus = Menu::where('is_active', true)// only active menus
            ->where('guard_name', $guardName)// currrent gaurd menus
            ->whereNull('parent_id')
            ->with(['children' => function ($query) use ($guardName) {
                $query->where('is_active', true)// only active child menus
                    ->where('guard_name', $guardName)// current gaurd menus 
                    ->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

           

        // Filter menus based on user permissions
        return $menus->filter(function ($menu) use ($user) {
            return $this->userCanAccessMenu($menu, $user);
        })->map(function ($menu) use ($user) {
            // Filter children as well
            if ($menu->children->isNotEmpty()) {
                $menu->setRelation('children', $menu->children->filter(function ($child) use ($user) {
                    return $this->userCanAccessMenu($child, $user);
                }));
            }
            return $menu; 
        });
    }

    /**
     * Check if user can access a menu item
     */
    private function userCanAccessMenu($menu, $user)
    {
        // Get menu permissions for this menu
        $menuPermissions = $menu->permissions()
            ->with(['role', 'permission'])
            ->get();

        // If no permissions assigned, menu is accessible to all
        if ($menuPermissions->isEmpty()) {
            return true;
        }

        // Check if user has any of the required role + permission combinations
        foreach ($menuPermissions as $menuPermission) {
            if ($user->hasRole($menuPermission->role->name) && 
                $user->hasPermission($menuPermission->permission->name)) {
                return true;
            }
        }

        return false;
    }
}