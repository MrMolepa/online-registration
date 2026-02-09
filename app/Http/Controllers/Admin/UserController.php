<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\Center;
use App\Models\District;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use App\Models\CandidateUser;
use App\Notifications\SendResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $providers = config('auth.providers');
        unset($providers['backpack']);
        unset($providers['candidates']);
        $user_types = collect($providers);
        $roles = Role::get();
        $districts = District::get();

        // Handle AJAX request for Admin Users DataTable
        if ($request->ajax() && $request->type === 'admin') {
            $users = AdminUser::with('roles')->get();

            return DataTables::of($users)
                ->setRowId('id')
                ->addColumn('state', function ($row) {
                    $status = $row->status == 1 ? "able" : "unable";
                    return "<div class='$status'></div>";
                })
                ->addColumn('profile_picture', function ($row) {
                    $profile = asset('adminAssets/assets/img/profile.png');
                    if (!empty($row->profile)) {
                        $profile = asset('uploads/profile/' . $row->profile);
                    }
                    return "<img src='$profile' alt='' width='40px'>";
                })
                ->addColumn('username', function ($row) {
                    return $row->username;
                })
                ->addColumn('occupation', function ($row) {
                    return $row->occupation;
                })
                ->addColumn('email', function ($row) {
                    return $row->email;
                })
                ->addColumn('action', function ($row) {
                    $font_color = $row->status == 1 ? "activate" : "deactivate";
                    $html = "<div class='actions'>
                                <a href='#' title='Edit' type='button' data-toggle='modal' data-target='#edit-user' data-id='$row->id' id='btn-edit-user'><i class='fas fa-user-edit'></i></a>
                                <a href='#' title='$row->status' class='change-status' data-user_status='$row->status' data-id='$row->id'> <i class='fas fa-signal $font_color'></i></a>
                                <a href='#' title='Change Password' type='button' data-toggle='modal' data-target='#change-password' data-id='$row->id' id='btn-edit-user-password'><i class='fas fa-unlock-alt'></i></a>
                                <a href='#' title='Manage Permissions' type='button' class='btn-manage-permissions' data-toggle='modal' data-target='#manage-permissions' data-id='$row->id'><i class='fas fa-shield-alt'></i></a>
                            </div>";
                    return $html;
                })
                ->rawColumns(['state', 'profile_picture', 'action'])
                ->make(true);
        }

        // Handle AJAX request for Center Users DataTable
        if ($request->ajax() && $request->type === 'center') {
            $users = User::get();

            return DataTables::of($users)
                ->setRowId('id')
                ->addColumn('state', function ($row) {
                    $status = $row->status == 1 ? "able" : "unable";
                    return "<div class='$status'></div>";
                })
                ->addColumn('profile_picture', function ($row) {
                    $profile = asset('adminAssets/assets/img/profile.png');
                    if (!empty($row->profile)) {
                        $profile = asset('uploads/profile/' . $row->profile);
                    }
                    return "<img src='$profile' alt='' width='40px'>";
                })
                ->addColumn('username', function ($row) {
                    return $row->username;
                })
                ->addColumn('center_no', function ($row) {
                    return $row->centers_no ?? 'N/A';
                })
                ->addColumn('occupation', function ($row) {
                    return $row->occupation;
                })
                ->addColumn('email', function ($row) {
                    return $row->email;
                })
                ->addColumn('action', function ($row) {
                    $font_color = $row->status == 1 ? "activate" : "deactivate";
                    $html = "<div class='actions'>
                                <a href='#' title='$row->status' class='change-status' data-user_status='$row->status' data-id='$row->id'> <i class='fas fa-signal $font_color'></i></a>
                            </div>";
                    return $html;
                })
                ->rawColumns(['state', 'profile_picture', 'action'])
                ->make(true);
        }

        // Handle AJAX request for Sponsor Users DataTable
        if ($request->ajax() && $request->type === 'sponsor') {
            // Assuming you have a Sponsor model, adjust the model name as needed
            $sponsors = DB::table('sponsors')->get(); // Replace with your actual sponsor model: Sponsor::with('districts')->get();

            return DataTables::of($sponsors)
                ->setRowId('id')
                ->addColumn('state', function ($row) {
                    $status = $row->status == 1 ? "able" : "unable";
                    return "<div class='$status'></div>";
                })
                ->addColumn('profile_picture', function ($row) {
                    $profile = asset('adminAssets/assets/img/profile.png');
                    if (!empty($row->profile)) {
                        $profile = asset('uploads/profile/' . $row->profile);
                    }
                    return "<img src='$profile' alt='' width='40px'>";
                })
                ->addColumn('username', function ($row) {
                    return $row->username ?? 'N/A';
                })
                ->addColumn('sponsor_key', function ($row) {
                    return $row->sponsor ?? 'N/A';
                })
                ->addColumn('level', function ($row) {
                    return $row->level ?? 'N/A';
                })
                ->addColumn('occupation', function ($row) {
                    return $row->occupation ?? 'N/A';
                })
                ->addColumn('email', function ($row) {
                    return $row->email;
                })
                ->addColumn('action', function ($row) {
                    $font_color = $row->status == 1 ? "activate" : "deactivate";
                    $editRoute = route('admin.sponsor-users.edit', $row->id);
                    $html = "<div class='actions'>
                                <a href='#' title='Edit' type='button' class='edit-sponsor' data-action='$editRoute'><i class='fas fa-user-edit'></i></a>
                                <a href='#' title='$row->status' class='change-status-sponsor' data-user_status='$row->status' data-id='$row->id'> <i class='fas fa-signal $font_color'></i></a>
                            </div>";
                    return $html;
                })
                ->rawColumns(['state', 'profile_picture', 'action'])
                ->make(true);
        }

        // Handle AJAX request for Candidate Users DataTable
        if ($request->ajax() && $request->type === 'candidate') {
            $candidateUsers = CandidateUser::query();

            return DataTables::of($candidateUsers)
                ->setRowId('id')
                ->addColumn('state', function ($row) {
                    $status = $row->status == 1 ? "able" : "unable";
                    return "<div class='$status'></div>";
                })
                ->addColumn('profile_picture', function ($row) {
                    $profile = asset('adminAssets/assets/img/profile.png');
                    if (!empty($row->profile)) {
                        $profile = asset('uploads/profile/' . $row->profile);
                    }
                    return "<img src='$profile' alt='' width='40px'>";
                })
                ->addColumn('username', function ($row) {
                    return $row->username;
                })
                ->addColumn('candidate_no', function ($row) {
                    return str_pad($row->candidate_no, 9, '0', STR_PAD_LEFT);
                })
                ->addColumn('center_no', function ($row) {
                    return $row->center_no ?? 'N/A';
                })
                ->addColumn('national_id', function ($row) {
                    return $row->national_id ?? 'N/A';
                })
                ->addColumn('session', function ($row) {
                    return $row->session ?? 'N/A';
                })
                ->addColumn('email', function ($row) {
                    return $row->email ?? 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $font_color = $row->status == 1 ? "activate" : "deactivate";
                    $html = "<div class='actions'>
                                <a href='#' title='$row->status' class='change-status-candidate' data-user_status='$row->status' data-id='$row->id'> <i class='fas fa-signal $font_color'></i></a>
                                <a href='#' title='Change Password' type='button' data-toggle='modal' data-target='#change-password-candidate' data-id='$row->id' class='btn-edit-candidate-password'><i class='fas fa-unlock-alt'></i></a>
                            </div>";
                    return $html;
                })
                ->rawColumns(['state', 'profile_picture', 'action'])
                ->make(true);
        }

        return view('admin.users.users', compact('roles', 'user_types', 'districts'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'occupation' => 'required',
            'email' => "required|unique:admins",
            'user_type' => 'required',
            'role' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $userType = $request->user_type;
        $user = new $userType();
        $username = strstr($request->email, '@', true);
        if ($request->hasFile("profileImage")) {
            $profile_pic = time() . '-' . $username . "." . $request->profileImage->extension();
            $request->profileImage->move(public_path('uploads/profile'), $profile_pic);
            $user->profile = $profile_pic;
        }
        $user->username = $username;
        $user->occupation = $request->occupation;
        $user->email = $request->email;
        $user->status = 1;
        $user->save();
        $user->syncRoles([$request->role]);

        if ($user) {
            $verify = DB::table('password_resets')->where([
                ['email', $request->all()['email']]
            ]);

            if ($verify->exists()) {
                $verify->delete();
            }
            $token = generateToken();
            DB::table('password_resets')
                ->insert(
                    [
                        'email' => $request->all()['email'],
                        'token' => $token
                    ]
                );

            $user->notify(new SendResetPassword(route('admin.password.reset', $token)));
        }
        return response()->json(['success' => 'successfuly updated the records']);
    }

    public function edit($id)
    {
        $user = AdminUser::with('roles')->findOrFail($id);
        $profile = asset('adminAssets/assets/img/profile.png');

        if (!empty($user->profile)) {
            $profile = asset('uploads/profile/' . $user->profile);
        }
        return response()->json(['user' => $user, 'profile' => $profile]);
    }

    public function editProfile()
    {
        return view('admin.users.profile');
    }

    public function updateProfile(Request $request, $id)
    {
        $this->validate($request, [
            'occupation' => 'required',
            'email' => 'required|email',
        ]);
        $user = User::findOrFail($id);

        if ($request->hasFile("profileImage")) {
            $profile_pic = time() . '-' . $request->username . "." . $request->profileImage->extension();
            $request->profileImage->move(public_path('uploads/profile'), $profile_pic);
            $user->profile = $profile_pic;
        }
        $user->occupation = $request->occupation;
        $user->email = $request->email;
        $user->save();
        return redirect()->back()->with('success', 'successfuly updated the records');
    }

    public function createAllCenterUser()
    {
        set_time_limit(0);
        $users = User::where('user_type', '=', 'center')->get();
        foreach ($users as $user) {
            $user->roles()->detach(3);
            $user->delete();
        }

        $centers = Center::get();
        foreach ($centers as $center) {
            $password = bin2hex(random_bytes(3));
            $user = new User();
            $user->user_type = 'center';
            $user->occupation = $center->district;
            $user->username = $center->center_no;
            $user->center_no = $center->center_no;
            $user->centre_account_password = $password;
            $user->center_name = $center->center_name;
            $user->password = Hash::make($password);
            $user->save();
            $user->syncRoles([3]);
        }

        return redirect()->back();
    }

    public function editPassword()
    {
        return view('admin.users.setting');
    }

    public function updatePassword(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $userPassword = $user->password;

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|same:confirm_password|min:6',
            'confirm_password' => 'required',
        ]);

        if (!Hash::check($request->current_password, $userPassword)) {
            return back()->withErrors(['current_password' => 'password not match']);
        }

        $user->password = Hash::make($request->password);
        $user->save();
        return redirect()->back()->with('success', 'password successfully updated');
    }

    public function changeUserStatus(Request $request)
    {
        $status = "";
        $message = "";
        if ($request->status == 0) {
            $status = 1;
            $message = "You have successfully activated the user";
        } else {
            $status = 0;
            $message = "You have successfully deactivated the user";
        }
        User::where('id', '=', $request->userid)->update(['status' => $status]);
        return response()->json(['status' => 1, 'message' => $message]);
    }

    public function changeSponsorStatus(Request $request)
    {
        $status = "";
        $message = "";
        if ($request->status == 0) {
            $status = 1;
            $message = "You have successfully activated the sponsor";
        } else {
            $status = 0;
            $message = "You have successfully deactivated the sponsor";
        }
        // Replace 'sponsors' with your actual table/model name
        DB::table('sponsors')->where('id', '=', $request->userid)->update(['status' => $status]);
        return response()->json(['status' => 1, 'message' => $message]);
    }

    public function changeCandidateStatus(Request $request)
    {
        $status = "";
        $message = "";
        if ($request->status == 0) {
            $status = 1;
            $message = "You have successfully activated the candidate user";
        } else {
            $status = 0;
            $message = "You have successfully deactivated the candidate user";
        }
        CandidateUser::where('id', '=', $request->userid)->update(['status' => $status]);
        return response()->json(['status' => 1, 'message' => $message]);
    }

    public function changeCandidatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()]);
        }

        $user = CandidateUser::findOrFail($request->userid);
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'error' => ['current_password' => ["Current password does not match!"]]
            ]);
        }
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['status' => 1, 'message' => "Password successfully changed!"]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()]);
        }

        $user = User::findOrFail($request->username);
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'error' => ['current_password' => ["Current password does not match!"]]
            ]);
        }
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['status' => 1, 'message' => "Password successfully changed!"]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'occupation' => 'required',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $user = User::where('username', '=', $request->username)->firstOrFail();

        if ($request->hasFile("profileImage")) {
            $profile_pic = time() . '-' . $request->username . "." . $request->profileImage->extension();
            $request->profileImage->move(public_path('uploads/profile'), $profile_pic);
            $user->profile = $profile_pic;
        }
        $user->occupation = $request->occupation;
        $user->email = $request->email;
        $user->save();
        $user->syncRoles([$request->role]);

        return response()->json(['status' => 1, 'message' => 'successfully updated records']);
    }


    /**
     * Get user permissions management data (AJAX)
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserPermissions($id)
    {
        try {
            $user = AdminUser::findOrFail($id);
            $allPermissions = Permission::orderBy('display_name')->get();

            // Get role name - check both direct role and Laratrust roles
            $roleName = 'No Role';
            $roleId = null;

            if ($user->role_id) {
                $role = Role::find($user->role_id);
                if ($role) {
                    $roleName = $role->display_name;
                    $roleId = $role->id;
                }
            } elseif (method_exists($user, 'roles') && $user->roles->count() > 0) {
                // Get from Laratrust roles
                $roleName = $user->roles->pluck('display_name')->implode(', ');
                $roleId = $user->roles->first()->id;
            }

            // Get role permissions directly from permission_role table
            $rolePermissionIds = [];
            if ($roleId) {
                $rolePermissionIds = \DB::table('permission_role')
                    ->where('role_id', $roleId)
                    ->pluck('permission_id')
                    ->toArray();
            }

            \Log::info('Role permissions found', [
                'role_id' => $roleId,
                'role_name' => $roleName,
                'permission_count' => count($rolePermissionIds),
                'permission_ids' => $rolePermissionIds
            ]);

            // Get user-specific permissions
            $userSpecificPermissions = [];
            try {
                // Try to get from permission_user table
                $userPerms = \DB::table('permission_user')
                    ->where('user_id', $user->id)
                    ->where('user_type', get_class($user))
                    ->get();

                foreach ($userPerms as $perm) {
                    $userSpecificPermissions[] = [
                        'id' => $perm->permission_id,
                        'allowed' => isset($perm->allowed) ? (bool) $perm->allowed : true,
                    ];
                }

                \Log::info('User-specific permissions found', [
                    'user_id' => $user->id,
                    'permission_count' => count($userSpecificPermissions)
                ]);
            } catch (\Exception $e) {
                \Log::error('Error getting user permissions: ' . $e->getMessage());
            }

            // Build effective permissions
            $effectivePermissions = [];

            // Add role permissions
            foreach ($rolePermissionIds as $permId) {
                $effectivePermissions[] = [
                    'id' => $permId,
                    'source' => 'role',
                    'allowed' => true,
                ];
            }

            // Add/override with user-specific permissions
            foreach ($userSpecificPermissions as $userPerm) {
                // Remove from effective if exists
                $effectivePermissions = array_filter($effectivePermissions, function ($ep) use ($userPerm) {
                    return $ep['id'] !== $userPerm['id'];
                });

                // Add user permission
                $effectivePermissions[] = [
                    'id' => $userPerm['id'],
                    'source' => 'user',
                    'allowed' => $userPerm['allowed'],
                ];
            }

            // Get full permission details for effective permissions
            $effectivePermissionsWithDetails = collect($effectivePermissions)->map(function ($ep) use ($allPermissions) {
                $permission = $allPermissions->firstWhere('id', $ep['id']);
                if ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'display_name' => $permission->display_name,
                        'description' => $permission->description,
                        'source' => $ep['source'],
                        'allowed' => $ep['allowed'],
                    ];
                }
                return null;
            })->filter()->values();

            // Format user-specific permissions for response
            $userSpecificPermsFormatted = collect($userSpecificPermissions)->map(function ($up) use ($allPermissions) {
                $permission = $allPermissions->firstWhere('id', $up['id']);
                if ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'allowed' => $up['allowed'],
                    ];
                }
                return null;
            })->filter()->values();

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role_id' => $roleId,
                    'role_name' => $roleName,
                ],
                'all_permissions' => $allPermissions,
                'effective_permissions' => $effectivePermissionsWithDetails,
                'user_specific_permissions' => $userSpecificPermsFormatted,
                'role_permission_ids' => $rolePermissionIds, // For debugging
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getUserPermissions: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load permissions: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Assign individual permission to user (AJAX)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignUserPermission(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'permission_id' => 'required|exists:permissions,id',
                'allowed' => 'required|in:0,1,true,false',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ]);
            }

            $user = AdminUser::findOrFail($id);
            $permission = Permission::findOrFail($request->permission_id);

            // Convert to boolean
            $allowed = filter_var($request->allowed, FILTER_VALIDATE_BOOLEAN);

            $user->givePermission($permission, $allowed);

            return response()->json([
                'success' => true,
                'message' => 'Permission ' . ($allowed ? 'allowed' : 'denied') . ' successfully.',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User or permission not found.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error assigning permission: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign permission: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove user-specific permission (AJAX)
     *
     * @param  int  $userId
     * @param  int  $permissionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function revokeUserPermission($userId, $permissionId)
    {
        $user = AdminUser::findOrFail($userId);
        $permission = Permission::findOrFail($permissionId);

        $user->revokePermission($permission);

        return response()->json([
            'success' => true,
            'message' => 'User-specific permission removed. User will inherit from role.',
        ]);
    }

    /**
     * Update user role (AJAX)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUserRole(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'nullable|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = AdminUser::findOrFail($id);
        $user->role_id = $request->role_id;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully.',
            'role' => $user->role ? $user->role->only(['id', 'name', 'display_name']) : null,
        ]);
    }

    public function destroy($id)
    {
        //
    }
}