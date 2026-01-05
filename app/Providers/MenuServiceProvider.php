<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }



public function boot()
{
    view()->composer(
    ['admin.partials.sidebar', 'school.partials.sidebar', 'candidate.partials.sidebar'],
    function ($view) {

        $user = auth()->user();
        if (!$user) {
            return;
        }

        //  direct
     $directPermissionIds = $user->permissions->pluck('id')->toArray();


    //permissions ONLY from roles (not direct)
        $permissionIds = $user->roles
                    ->load('permissions')
                    ->pluck('permissions')
                    ->flatten()
                    ->pluck('id')
                    ->unique()
                    ->toArray();


$merged = array_merge($directPermissionIds,  $permissionIds);

        // 2. Get all menu IDs this user has access to
        $allowedMenuIds = DB::table('menu_permission')
            ->whereIn('permission_id',$merged)
            ->pluck('menu_id')
            ->toArray();

        // 3. Load allowed parent menus
        $parents = Menu::whereNull('parent_id')
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
            ->where('guard_name', $guardName)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) use ($guardName) {
                $query->where('is_active', true)
                    ->where('guard_name', $guardName)
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
                return $menu->children->count() > 0;
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
            ->with(['role', 'permission'])// 
            ->get();

        // If no permissions assigned, menu is accessible to all
        if ($menuPermissions->isEmpty()) {
            return false;
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
