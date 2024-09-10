<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\AdminUser;
use App\Models\Center;
use App\Models\District;
use App\Models\Role;
use App\Models\User;
use App\Notifications\SendResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;



use Illuminate\Support\Facades\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


        // Password::sendResetLink( 'posholim@examscouncil.org.ls'
        // );
        // Mail::to('posholim@examscouncil.org.ls')->send(new SendResetPassword());


        $providers = config('auth.providers');

        unset($providers['backpack']);
        unset($providers['candidates']);
        $user_types = collect($providers);
        $roles = Role::get();
        $districts = District::get();
        return view('admin.users.users', compact('roles', 'user_types','districts'));
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
        $username  = strstr($request->email, '@', true);
        if ($request->hasFile("profileImage")) {
            $profile_pic = time() . '-' . $username . "." . $request->profileImage->extension();
            $request->profileImage->move(public_path('uploads/profile'), $profile_pic);
            $user->profile =  $profile_pic;
        }
        $user->username = $username;
        $user->occupation = $request->occupation;
        $user->email = $request->email;
        $user->status = 1;
        $user->save();
        $user->syncRoles([$request->role]);

        if ($user) {
            $verify =  DB::table('password_resets')->where([
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
        return response()->json(['success'=> 'successfuly updated the records']);
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
            $user->profile =  $profile_pic;
        }
        $user->occupation = $request->occupation;
        $user->email = $request->email;
        $user->save();
        return redirect()->back()->with('success', 'successfuly updated the records');
    }

    public function getAllUsers()
    {


        $tablecenter = '<table class="table table-striped" id="users_table">
                <thead>
                    <tr>
                       <th>State</th>
                       <th >Profile Picture</th>
                        <th>User Id</th>
                        <th>Centre No</th>
                        <th>Occupation</th>
                        <th>Email</th>
                        <th colspan="3">Action</th>
                    </tr>
                </thead>';
        $users = User::get();



        foreach ($users as $user) {

            $status = "unable ";
            $font_color = "deactivate";
            $profile = asset('adminAssets/assets/img/profile.png');
            if ($user->status == 1) {
                $status = "able";
                $font_color = "activate";
            }
            if (!empty($user->profile)) {
                $profile = asset('uploads/profile/' . $user->profile);
            }

            $tablecenter .= '<tr>
                            <td><div class="' . $status . '"></div> </td>
                            <td class="profile-pic"><img src="' . $profile . '" alt="" width="40px"></td>
                            <td>' . $user->username . '</td>

                            <td>' . $user->centers_no . '</td>
                            <td>' . $user->occupation . '</td>
                            <td>' . $user->email . '</td>
                            <td class="btns-action">
                                <div class="actions">
                                    <a href="#" title="' . $user->status . '" class="change-status" data-user_status="' . $user->status . '" data-id="' . $user->id . '" > <i class="fas fa-signal  ' . $font_color . '"></i></a>
                                </div>
                            </td>
                    </tr>';
        }
        $tablecenter .= '</tbody>
                 </table>';

        $tableAdmin = '<table class="table table-striped" id="users_table">
                <thead>
                    <tr>
                       <th>State</th>
                       <th >Profile Picture</th>
                        <th>User Id</th>
                        <th>Occupation</th>
                        <th>Email</th>
                        <th colspan="3">Action</th>
                    </tr>
                </thead>';
        $users = AdminUser::get();
        $status = " ";
        foreach ($users as $user) {
            // $user->syncRoles([2]);
            $status = "unable ";
            $font_color = "deactivate";
            $profile = asset('adminAssets/assets/img/profile.png');
            if ($user->status == 1) {
                $status = "able";
                $font_color = "activate";
            }
            if (!empty($user->profile)) {
                $profile = asset('uploads/profile/' . $user->profile);
            }

            $tableAdmin .= '<tr>
                            <td><div class="' . $status . '"></div> </td>
                            <td class="profile-pic"><img src="' . $profile . '" alt="" width="40px"></td>
                            <td>' . $user->username . '</td>


                            <td>' . $user->occupation . '</td>
                            <td>' . $user->email . '</td>
                            <td class="btns-action">
                                <div class="actions">
                                    <a href="#" title="Edit" type="button" data-toggle="modal" data-target="#edit-user" data-id="' . $user->id . '" id="btn-edit-user"><i class="fas fa-user-edit"></i></a>
                                    <a href="#" title="' . $user->status . '" class="change-status" data-user_status="' . $user->status . '" data-id="' . $user->id . '" > <i class="fas fa-signal   ' . $font_color . '"></i></a>
                                    <a href="#" title="Change Password" type="button" data-toggle="modal" data-target="#change-password" data-id="' . $user->id . '"  id="btn-edit-user-password"><i class="fas fa-unlock-alt"></i></a>
                                </div>
                            </td>
                    </tr>';
        }
        $tableAdmin .= '</tbody>
                 </table>';
        return response()->json(['status' => 'success', 'tableAdmin' =>  $tableAdmin, 'tablecenter' =>  $tablecenter]);
    }
    public function createAllCenterUser()
    {
        set_time_limit(0);
        $users = User::where('user_type', '=', 'center')->get();
        foreach ($users as  $user) {
            $user->roles()->detach(3);
            $user->delete();
        }


        $centers = Center::get();
        foreach ($centers  as  $center) {
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
            // Center id
            $user->syncRoles([3]);
        }

        return redirect()->back();
    }
    public function editPassword()
    {
        return view('admin.users.setting');
    }
    public function updatePassword(Request  $request)
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

        $user  = User::findOrFail($request->username);
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
            $user->profile =  $profile_pic;
        }
        $user->occupation = $request->occupation;
        $user->email = $request->email;
        $user->save();
        $user->syncRoles([$request->role]);

        // $role->permissions
        return response()->json(['status' => 1, 'message' => 'successfully updated records']);
    }

    public function destroy($id)
    {
        //
    }
}
