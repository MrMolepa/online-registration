<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
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
            $permissions = Permission::query();

            return DataTables::of($permissions)
                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.permissions.edit', $row->id);
                    $deleteForm = 'delete-permission-form-' . $row->id;
                    
                    $html = '<div class="btn-group" role="group">';
                    $html .= '<a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="Edit">';
                    $html .= '<i class="fa fa-edit"></i> Edit</a> ';
                    $html .= '<button type="button" class="btn btn-sm btn-danger btn-delete-permission" ';
                    $html .= 'data-id="' . $row->id . '" data-name="' . htmlspecialchars($row->display_name) . '" ';
                    $html .= 'title="Delete"><i class="fa fa-trash"></i> Delete</button>';
                    $html .= '</div>';
                    
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.permissions.permissions');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.permissions.create');
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
            'resource' => 'required|max:30',
            'permissions' => 'present|array',
            'permissions.*' => 'required',
        ]);

        foreach ($request->permissions as $permission) {
            $slug = strtolower($request->resource) . "-" . strtolower($permission);
            $diplayName = ucwords($permission . " " . $request->resource);
            $description = "Allow a user to " . strtoupper($permission) . " a " . ucwords($request->resource);

            Permission::create([
                'name' => $slug,
                'display_name' => $diplayName,
                'description' => $description,
            ]);
        }
        
        // Check if request is AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'You have successfully added permissions'
            ]);
        }
        
        return redirect(route('admin.permissions.index'))->with('success', 'You have successfully added permissions');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $permission = Permission::findOrFail($id);
        
        // Check if request is AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'permission' => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'display_name' => $permission->display_name,
                    'description' => $permission->description
                ]
            ]);
        }
        
        return view('admin.permissions.edit', compact('permission'));
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
            'name' => 'required|max:50',
            'display_name' => 'required|max:100',
            'description' => 'nullable|max:255',
        ]);

        $permission = Permission::findOrFail($id);
        $permission->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
        ]);

        // Check if request is AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully',
                'permission' => $permission
            ]);
        }

        return redirect(route('admin.permissions.index'))->with('success', 'Permission updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *                 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $permission = Permission::findOrFail($id);
            
            // Check if permission is assigned to any roles or users
            $rolesCount = $permission->roles()->count();
            $usersCount = \DB::table('permission_user')
                ->where('permission_id', $id)
                ->count();
            
            if ($rolesCount > 0 || $usersCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete permission. It is currently assigned to ' . 
                                ($rolesCount > 0 ? $rolesCount . ' role(s)' : '') . 
                                ($rolesCount > 0 && $usersCount > 0 ? ' and ' : '') .
                                ($usersCount > 0 ? $usersCount . ' user(s)' : '') . '.'
                ]);
            }
            
            $permission->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting permission: ' . $e->getMessage()
            ]);
        }
    }
}