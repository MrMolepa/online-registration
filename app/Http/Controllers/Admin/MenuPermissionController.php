<?php

namespace app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuPermission;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Config;
use Yajra\DataTables\Facades\DataTables;

class MenuPermissionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $permissions = MenuPermission::with(['menu', 'role', 'permission']);

            return DataTables::of($permissions)
                ->addColumn('menu_name', function($permission) {
                    return $permission->menu ? $permission->menu->name : '-';
                })
                ->addColumn('permission_name', function($permission) {
                    return $permission->permission ? $permission->permission->name : '-';
                })
                ->addColumn('role_name', function($permission) {
                    return $permission->role ? $permission->role->name : '-';
                })
                ->addColumn('actions', function($permission) {
                    return '<button class="btn btn-danger btn-sm delete-btn" data-url="' . route('admin.menu-permissions.destroy', $permission->id) . '">Delete</button>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return response()->json(['success' => false, 'message' => 'Invalid request']);
    }

    public function store(Request $request)
    {
        // $guards = array_keys(config('auth.guards'));


        $validated = $request->validate([
            'menu_id' => 'required|exists:menus,id',     
            'permission_id' => 'required|exists:permissions,id',
            'role_id' => 'required|exists:roles,id',
            //'guard_name' => ['required', 'string'],

        ]);

        // Check if this permission already exists
        $exists = MenuPermission::where('menu_id', $validated['menu_id'])
            ->where('permission_id', $validated['permission_id'])
            ->where('role_id', $validated['role_id'])
            // ->where('guard_name', $validated['guard_name'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This permission is already assigned'
            ]);
        }

        $permission = MenuPermission::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Permission assigned successfully',
            'permission' => $permission->load(['menu', 'role', 'permission'])
        ]);
    }

    public function destroy(MenuPermission $menuPermission)
    {
        $menuPermission->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Permission removed successfully'
        ]);
    }

     public function getGuards()
    {
        $guards = array_keys(config('auth.guards'));
        return response()->json([
            'success' => true,
            'guards' => $guards,
        ]);
    }

    //get permissions by menu for editing assignments purpose 
    public function getByMenu(Menu $menu)
    {
        $assignedPermissions = MenuPermission::where('menu_id', $menu->id)
            ->with(['role', 'permission'])
            ->get();

        $roles = Role::pluck('name', 'id');
        $permissions = Permission::pluck('name', 'id');

        return response()->json([
            'success' => true,
            'assigned_permissions' => $assignedPermissions,
            'roles' => $roles,
            'permissions' => $permissions,
            'menu' => $menu
        ]);
    }
}
