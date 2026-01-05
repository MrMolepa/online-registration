<?php

namespace app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MenuPermissionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $menus = Menu::with('permissions');
            return DataTables::of($menus)
                ->addColumn('menu_name', function ($menu) {
                    return $menu->name;
                })
                ->addColumn('permission_name', function ($permission) {
                    return $permission->permission ? $permission->permission->name : '-';
                })
                ->addColumn('role_name', function ($permission) {
                    return $permission->role ? $permission->role->name : '-';
                })
                ->addColumn('actions', function ($permission) {
                    return '<button class="btn btn-danger btn-sm delete-btn" data-url="' . route('admin.menu-permissions.destroy', $permission->id) . '">Delete</button>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return response()->json(['success' => false, 'message' => 'Invalid request']);
    }
    /**
     * Assign permission to menu
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'permission_id' => 'required|exists:permissions,id',
        ]);

        $menu = Menu::findOrFail($validated['menu_id']);

        // 🔍 Check for duplicate BEFORE saving
        $exists = DB::table('menu_permission')
            ->where('menu_id', $request->menu_id)
            ->where('permission_id', $request->permission_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'errors' => [
                    'permission_id' => ['This permission is already assigned to this menu.']
                ]
            ], 422);
        }

        // ✅ DO NOT overwrite existing permissions
        $menu->permissions()->syncWithoutDetaching([
            $validated['permission_id']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permission assigned successfully',
            'permission' => Permission::find($validated['permission_id'])
        ]);
    }

    /**
     * Remove permission from menu (pivot detach)
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'permission_id' => 'required|exists:permissions,id',
        ]);

        $menu = Menu::findOrFail($request->menu_id);
        $menu->permissions()->detach($request->permission_id);

        return response()->json([
            'success' => true,
            'message' => 'Permission removed successfully'
        ]);
    }

    /**
     * Guards (optional)
     */
    public function getGuards()
    {
        return response()->json([
            'success' => true,
            'guards' => array_keys(config('auth.guards'))
        ]);
    }

    /**
     * Get permissions for a menu (for modal)
     */
    public function getByMenu(Menu $menu)
    {
        return response()->json([
            'success' => true,
            'assigned_permissions' => $menu->permissions, // ✅ pivot-based
            'permissions' => Permission::pluck('name', 'id'),
            'menu' => $menu
        ]);
    }
}
