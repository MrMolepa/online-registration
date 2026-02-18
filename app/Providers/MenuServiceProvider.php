<?php


namespace App\Providers;


use App\Models\Menu;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class MenuServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }




    private function getUserGuard($guard, $user)
    {






        if ($guard == 'candidate') {
            $all_candidate_user = DB::table('candidate_users')
                ->where('id', $user->id)
                ->pluck('id')->toArray();
            $user_type = get_class($user);
            $user_roles = DB::table('role_user')
                ->whereIn('user_id', $all_candidate_user)
                ->where('user_type', $user_type)
                ->first();


            if (!$user_roles) {
                $user->syncRoles([9]); // Assign 'Candidate' role by default
            }
        }
    }


    public function boot()
    {
        view()->composer(
            [
                'admin.partials.sidebar',
                'school.partials.sidebar',
                'candidate.partials.sidebar',
                'sponsor.partials.sidebar',
                'web.partials.sidebar',
            ],
            function ($view) {


                /* -------------------------------------------------
                 | 1. Resolve authenticated user & guard
                 ------------------------------------------------- */
                $guard = Auth::getDefaultDriver();
                $user = Auth::guard($guard)->user();
                $this->getUserGuard($guard, $user);


                if (!$user) {
                    return;
                }


                /* -------------------------------------------------
  | 2. Collect ALL user permission IDs
  |    (direct + via roles)
  ------------------------------------------------- */
                $directPermissionIds = $user->permissions
                    ->pluck('id')
                    ->toArray();

                // Get role permissions (single role, not multiple)
                $rolePermissionIds = [];
                if ($user->role) {
                    $rolePermissionIds = $user->role->permissions
                        ->pluck('id')
                        ->toArray();
                }

                $permissionIds = array_unique(
                    array_merge($directPermissionIds, $rolePermissionIds)
                );
                
                if (empty($permissionIds)) {
                    $view->with('dynamicMenus', collect());
                    return;
                }


                /* -------------------------------------------------
                 | 3. Get menu IDs allowed by permission
                 ------------------------------------------------- */
                $allowedMenuIds = DB::table('menu_permission')
                    ->whereIn('permission_id', $permissionIds)
                    ->pluck('menu_id')
                    ->unique()
                    ->toArray();


                if (empty($allowedMenuIds)) {
                    $view->with('dynamicMenus', collect());
                    return;
                }


                /* -------------------------------------------------
                 | 4. Load parent menus
                 |    - Parent is allowed OR
                 |    - Any child is allowed
                 ------------------------------------------------- */
                $parents = Menu::query()
                    ->whereNull('parent_id')
                    ->where('guard_name', $guard)
                    ->where('is_active', true)
                    ->where(function ($q) use ($allowedMenuIds) {
                        $q->whereIn('id', $allowedMenuIds)
                            ->orWhereHas('children', function ($q2) use ($allowedMenuIds) {
                                $q2->whereIn('id', $allowedMenuIds);
                            });
                    })
                    ->orderBy('order')
                    ->get();


                /* -------------------------------------------------
                 | 5. Load children (inherit parent permission)
                 ------------------------------------------------- */
                $parents->load([
                    'children' => function ($q) use ($guard) {
                        $q->where('guard_name', $guard)
                            ->where('is_active', true)
                            ->orderBy('order');
                    }
                ]);


                /* -------------------------------------------------
                 | 6. Apply inheritance logic
                 ------------------------------------------------- */
                $finalMenus = $parents->map(function ($menu) use ($allowedMenuIds) {


                    // 🔹 Parent has permission → ALL children visible
                    if (in_array($menu->id, $allowedMenuIds)) {
                        return $menu;
                    }


                    // 🔹 Parent NOT permitted → filter children
                    $filteredChildren = $menu->children->filter(function ($child) use ($allowedMenuIds) {
                        return in_array($child->id, $allowedMenuIds);
                    });


                    $menu->setRelation('children', $filteredChildren);


                    return $menu;
                })->filter(function ($menu) use ($allowedMenuIds) {
                    // Keep menu if:
                    // - Parent permitted
                    // - OR it has visible children
                    return in_array($menu->id, $allowedMenuIds)
                        || $menu->children->isNotEmpty();
                });


                /* -------------------------------------------------
                 | 7. Share with views
                 ------------------------------------------------- */
                $view->with('dynamicMenus', $finalMenus);
            }
        );
    }
}
