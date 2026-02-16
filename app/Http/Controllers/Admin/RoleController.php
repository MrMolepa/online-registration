<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Handle AJAX request for DataTables
        if ($request->ajax()) {
            $roles = Role::query();

            return DataTables::of($roles)
                ->addColumn('permissions', function ($row) {
                    $permissionsUrl = route('admin.roles.show', $row->id);
                    return '<a href="' . $permissionsUrl . '" class="btn btn-sm btn-info">' .
                           '<i class="fas fa-key"></i> Permissions</a>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.roles.edit', $row->id);
                    
                    $html = '<div class="btn-group" role="group">';
                    $html .= '<a href="' . $editUrl . '" class="btn btn-sm btn-primary btn-edit-role" ';
                    $html .= 'data-id="' . $row->id . '">';
                    $html .= '<i class="fas fa-edit"></i> Edit</a> ';
                    $html .= '<button type="button" class="btn btn-sm btn-danger btn-delete-role" ';
                    $html .= 'data-id="' . $row->id . '" data-name="' . htmlspecialchars($row->display_name) . '">';
                    $html .= '<i class="fas fa-trash"></i> Delete</button>';
                    $html .= '</div>';
                    
                    return $html;
                })
                ->rawColumns(['permissions', 'action'])
                ->make(true);
        }

        $roles = Role::paginate(10);
        return view('admin.roles.roles', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::get();
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'display_name' => 'required|max:255',
            'description' => 'required|max:255',
            'name' => 'required|max:100|alpha_dash|unique:roles,name',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
        ]);

        // Check if request is AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Successfully created the role',
                'role' => $role
            ]);
        }

        return redirect(route('admin.roles.index'))->with("success", "Successfully created the role");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::get();
        return view('admin.roles.permissionRoles', ['role' => $role, 'permissions' => $permissions]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::get();

        // Check if request is AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'role' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $role->display_name,
                    'description' => $role->description
                ]
            ]);
        }

        return view('admin.roles.edit', ['role' => $role, 'permissions' => $permissions]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'display_name' => 'required|max:255',
            'description' => 'required|max:255',
        ]);

        $role = Role::findOrFail($id);
        $role->display_name = $request->display_name;
        $role->description = $request->description;
        $role->save();

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        // Check if request is AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Successfully updated the role',
                'role' => $role
            ]);
        }

        return redirect(route('admin.roles.index'))->with("success", "Successfully updated the role");
    }

    public function updateRolePermission(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }
        return redirect(route('admin.roles.index'))->with("success", "Successfully updated the records");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        try {
            $role = Role::findOrFail($id);

            // Check if role has users assigned
            $usersCount = $role->users()->count();

            if ($usersCount > 0) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete role. It is currently assigned to ' . $usersCount . ' user(s).'
                    ]);
                }
                return redirect()->back()->with("error", "Cannot delete role. It is currently assigned to users.");
            }

            // Regular Delete
            $role->delete();

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully deleted the role'
                ]);
            }

            return redirect()->back()->with("success", "Successfully deleted the role");
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting role: ' . $e->getMessage()
                ]);
            }
            return redirect()->back()->with("error", "Error deleting role");
        }
    }

    /**
     * Assign permissions to a role (AJAX)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignPermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'permissions' => 'required|array', 
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->syncPermissions($validated['permissions']);

        return response()->json([
            'success' => true,
            'message' => 'Permissions assigned successfully.',
            'permissions_count' => count($validated['permissions']),
        ]);
    }

    /**
     * Remove a permission from a role (AJAX)
     *
     * @param  int  $roleId
     * @param  int  $permissionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function revokePermission($roleId, $permissionId)
    {
        $role = Role::findOrFail($roleId);
        $permission = Permission::findOrFail($permissionId);

        $role->revokePermission($permission);

        return response()->json([
            'success' => true,
            'message' => 'Permission revoked successfully.',
        ]);
    }

    /**
     * Get role users (AJAX)
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoleUsers($id)
    {
        $role = Role::with('users')->findOrFail($id);

        return response()->json([
            'success' => true,
            'users' => $role->users,
            'count' => $role->users->count(),
        ]);
    }
}