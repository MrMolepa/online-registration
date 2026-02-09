<?php
// app/Http/Controllers/Admin/UserPermissionController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class UserPermissionController extends Controller
{
    /**
     * Display user permissions management page
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $user = User::with('role', 'permissions')->findOrFail($id);
        $allPermissions = Permission::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $effectivePermissions = $user->getAllPermissions();
        
        return view('admin.user-permissions.index', compact(
            'user',
            'allPermissions',
            'roles',
            'effectivePermissions'
        ));
    }

    /**
     * Show all users with their permissions
     *
     * @return \Illuminate\Http\Response
     */
    public function usersList()
    {
        $users = User::with('role')->paginate(15);
        return view('admin.user-permissions.users-list', compact('users'));
    }

    /**
     * Assign individual permission to user (AJAX)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assign(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'permission_id' => 'required|exists:permissions,id',
            'allowed' => 'required|boolean',
        ]);

        $permission = Permission::findOrFail($validated['permission_id']);
        $user->givePermission($permission, $validated['allowed']);

        return response()->json([
            'success' => true,
            'message' => 'Permission ' . ($validated['allowed'] ? 'allowed' : 'denied') . ' successfully.',
            'permission' => [
                'id' => $permission->id,
                'name' => $permission->name,
                'display_name' => $permission->display_name,
            ],
            'allowed' => $validated['allowed'],
        ]);
    }

    /**
     * Remove individual permission from user (AJAX)
     *
     * @param  int  $userId
     * @param  int  $permissionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function revoke($userId, $permissionId)
    {
        $user = User::findOrFail($userId);
        $permission = Permission::findOrFail($permissionId);
        
        $user->revokePermission($permission);

        return response()->json([
            'success' => true,
            'message' => 'User-specific permission removed. User will inherit from role.',
        ]);
    }

    /**
     * Bulk update user permissions (AJAX)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*.id' => 'required|exists:permissions,id',
            'permissions.*.allowed' => 'required|boolean',
        ]);

        foreach ($validated['permissions'] as $permissionData) {
            $user->givePermission($permissionData['id'], $permissionData['allowed']);
        }

        return response()->json([
            'success' => true,
            'message' => 'User permissions updated successfully.',
        ]);
    }

    /**
     * Get user's effective permissions (AJAX)
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEffectivePermissions($id)
    {
        $user = User::findOrFail($id);
        $effectivePermissions = $user->getAllPermissions();

        return response()->json([
            'success' => true,
            'permissions' => $effectivePermissions,
        ]);
    }

    /**
     * Assign role to user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function assignRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'role_id' => 'nullable|exists:roles,id',
        ]);

        $user->role_id = $validated['role_id'];
        $user->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Role assigned successfully.',
                'role' => $user->role ? $user->role->only(['id', 'name', 'display_name']) : null,
            ]);
        }

        return redirect()->back()->with('success', 'Role assigned successfully.');
    }

    /**
     * Check if user has a specific permission (AJAX)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPermission(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'permission' => 'required|string',
        ]);

        $hasPermission = $user->hasPermission($validated['permission']);

        return response()->json([
            'success' => true,
            'has_permission' => $hasPermission,
            'permission' => $validated['permission'],
        ]);
    }
}