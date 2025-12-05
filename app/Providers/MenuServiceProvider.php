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
    // public function boot(): void
    // {
    //     // Share menu data with sidebar view
    //     View::composer('admin.partials.sidebar', function ($view) {
    //         $menus = $this->getAuthorizedMenus();
    //         $view->with('dynamicMenus', $menus);
    //     });
    // }
//  public function boot()
// {
//     view()->composer('admin.partials.sidebar', function ($view) {

//         $user = auth()->user();
//         if (!$user) {
//             return; // no user logged in
//         }

//         // Get role IDs
//         $roleIds = $user->roles->pluck('id')->toArray();

//         // Menu permission IDs
//         $allowedMenuIds = \DB::table('menu_permissions')
//             ->whereIn('role_id', $roleIds)
//             ->pluck('menu_id')
//             ->toArray();

//         // Load ALL allowed menus (parents + singles)
//         $menus = \App\Models\Menu::whereIn('id', $allowedMenuIds)
//             ->orderBy('order', 'asc')
//             ->with(['children' => function ($q) use ($allowedMenuIds) {
//                 $q->whereIn('id', $allowedMenuIds)
//                   ->orderBy('order', 'asc');
//             }])
//             ->get();

//         // Only show parent menus (parent_id = null)
//         $parents = $menus->whereNull('parent_id');

//         // Filter parents:
//         // - show parents that have children
//         // - or parents that are single menu items (no children)
//         $finalMenus = $parents->filter(function ($menu) {
//             return $menu->children->isNotEmpty() || is_null($menu->parent_id);
//         });

//         $view->with('dynamicMenus', $finalMenus);
//     });
// }

public function boot()
{
    view()->composer('admin.partials.sidebar', function ($view) {

        $user = auth()->user();
        if (!$user) {
            return;
        }

        // 1. Get user role IDs
        $roleIds = $user->roles->pluck('id')->toArray();

        // 2. Get all menu IDs this user has access to
        $allowedMenuIds = \DB::table('menu_permissions')
            ->whereIn('role_id', $roleIds)
            ->pluck('menu_id')
            ->toArray();

        // 3. Load allowed parent menus
        $parents = \App\Models\Menu::whereNull('parent_id')
            ->whereIn('id', $allowedMenuIds)  // only allowed parents
            ->orderBy('order', 'asc')
            ->get();

        // 4. For each allowed parent → load ALL its children (no filtering)
        $parents->load(['children' => function ($q) {
            $q->orderBy('order', 'asc');
        }]);



        



        // 5. Filter: show parents that either:
        //    - have children
        //    - OR are single menus
        $finalMenus = $parents->filter(function ($menu) {
            return $menu->children->isNotEmpty() || is_null($menu->parent_id);
        });

        $view->with('dynamicMenus', $finalMenus);
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
