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
            $menus = $this->getAuthorizedMenus();
            $view->with('dynamicMenus', $menus);
        });
    }

    /**
     * Get menus authorized for the current user
     */
    private function getAuthorizedMenus()
    {
        if (!Auth::check()) {
            return collect([]);
        }

        $user = Auth::user();
        $guardName = Auth::getDefaultDriver();

        // Get all active parent menus with their children
        $menus = Menu::where('is_active', true)
            ->where('guard_name', $guardName)// Fetch only menus for the current guard
            ->whereNull('parent_id')
            ->with(['children' => function ($query) use ($guardName) {
                $query->where('is_active', true)
                    ->where('guard_name', $guardName)// Fetch only active children for the same guard
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
                $filteredChildren = $menu->children->filter(function ($child) use ($user) {
                    return $this->userCanAccessMenu($child, $user);
                });
                $menu->setRelation('children', $filteredChildren);// Update children relation
            }
            return $menu;
        })->filter(function ($menu) {
            // Remove parent menus that have no accessible children
            if ($menu->children->isNotEmpty()) {
                return $menu->children->count() > 0;// Keep parent if it has accessible children
            }
            return true;
        });
    }

    /**
     * Check if user can access a menu item
     */
    private function userCanAccessMenu($menu, $user)// this method checks if the user has access to the menu based on permissions
    {
        // Get menu permissions for this menu
        $menuPermissions = $menu->permissions()// Fetch permissions
            ->with(['role', 'permission'])
            ->get();// this gets all permissions associated with the menu

        // If no permissions assigned, menu is accessible to all
        if ($menuPermissions->isEmpty()) {
            return true;
        }

        // Get user's roles
        $userRoles = $user->roles->pluck('id')->toArray();
        
        // Get user's permissions (both direct and through roles)
        $userPermissions = $user->permissions->pluck('id')->toArray();// Direct permissions
        
        // Also get permissions through roles
        $rolePermissions = $user->roles->flatMap(function ($role) {
            return $role->permissions->pluck('id');// Permissions from each role 
        })->toArray();
        
        // Merge all permissions
        $allUserPermissions = array_unique(array_merge($userPermissions, $rolePermissions));// Merged permissions array 

        // Check if user has any of the required role + permission combinations
        foreach ($menuPermissions as $menuPermission) {
            $hasRole = in_array($menuPermission->role_id, $userRoles);// Check role
            $hasPermission = in_array($menuPermission->permission_id, $allUserPermissions);// Check in merged permissions
            
            if ($hasRole && $hasPermission) {
                return true;
            }
        }

        return false;
    }
}