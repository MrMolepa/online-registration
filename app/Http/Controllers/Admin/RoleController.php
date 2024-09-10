<?php

namespace App\Http\Controllers\Admin;


use App\Models\Permission;
use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
            'name' => 'required|max:100|alpha_dash|unique:permissions,name',
        ]);


        Role::create([
            'name' => $request->name,
            'display_name' => $request->display_name, // optional
            'description' => $request->description, // optional
        ]);

        return redirect(route('admin.roles.index'))->with("success", "succefully update the records");
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
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::get();
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
        return redirect(route('admin.roles.index'))->with("success", "succefully update the records");
    }

    public function updateRolePermission(Request $request, $id)
    {

        $role = Role::findOrFail($id);
        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }
        return redirect(route('admin.roles.index'))->with("success", "succefully update the records");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        // Regular Delete
        $role->delete(); // This will work no matter what
        return redirect()->back()->with("success", "succefully deleted the records");
    }


    // public function getRoleUsers($roleId)
    // {
    //     return response()->json($this->roleUsersRepository->getRoleUsers($roleId));
    // }


}
