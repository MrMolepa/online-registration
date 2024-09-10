<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{

    public function index()
    {
        $roles = Role::whereIn('id', [3, 4])->get();
        return view('school.users', compact('roles'));
    }
    public function getAllUsers()
    {
        $center_no = auth()->user()->center_no;

        $users = User::where('user_type', '=', 'center_user')
            ->where('center_no', '=', $center_no)
            ->get();
        $output = '';
        $output = '<table class="table  table-striped" id="users_table">
			<thead>
				<tr>
				   <th>State</th>
				   <th >Profile Picture</th>
					<th>Username</th>
					<th>Centre No</th>
					<th>Occupation</th>
					<th>Email</th>
                    <th width="15%">Action</th>
				</tr>
			</thead>';

        $status = " ";

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

            $user_status = $user->status != 1 ? 'activete' : 'deactivate';
            $output .= '<tr>
		                <td><div class="' . $status . '"></div> </td>
						<td class="text-center profile-pic"><img src="' . $profile . '" alt="" width="40px"></td>
						<td>' . $user->username . '</td>
						<td>' . $user->center_no . '</td>
						<td>' . $user->occupation . '</td>
						<td>' . $user->email . '</td>
						<td class="btns-action"> <div class="actions">';


            if (auth()->user()->isAbleTo('users-update')) {
                $output .= '<a href="#" type="button" data-toggle="modal" title="Edit" data-target="#edit-user" data-id="' . $user->username . '"
                id="btn-edit-user"><i class="fas fa-user-edit"></i>
                <span class="tooltiptext">Edit </span>
                </a>';
            }

            if (auth()->user()->isAbleTo('users-update')) {
                $output .= '<a href="#" class="change-status" title="' . $user_status   . '" data-user_status="' . $user->status . '" data-id="' . $user->username . '" >  <i class="fas fa-signal ' . $font_color . '"></i>
                            <span class="tooltiptext">' . $user_status  . '</span>
                            </a>';
            }
            if (auth()->user()->isAbleTo('users-activate')) {
                $output .= '<a href="#"  title="Change Password" type="button" data-toggle="modal" data-target="#change-password" data-id="' . $user->id . '"  id="btn-edit-user-password"><i class="fas fa-unlock-alt"></i>
                <span class="tooltiptext">Change Password</span>
                </a>';
            }

            $output .= '</div></td>
				</tr>';
        }

        $output .= '</tbody>
			 </table>';

        return response()->json(['status' => 'success', 'table' => $output]);
    }

    public function addUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'occupation' => 'required',
            'email' => 'required|email',
        ]);
        $center_no = auth()->user()->center_no;



        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $user_number = User::where('center_no', '=', $center_no)->get()->count();

        if ($user_number < 3) {
            $user_number = User::where('center_no', '=', $center_no)->latest()->first()->id;
            $user_number += 1;
            $username = $center_no . $user_number;
            $user = new User();
            if ($request->hasFile("profileImage")) {
                $profile_pic = time() . '-' . $request->username . "." . $request->profileImage->extension();
                $request->profileImage->move(public_path('uploads/profile'), $profile_pic);
                $user->profile =  $profile_pic;
            }
            $user->center_no = $center_no;
            $user->username = $username;
            $user->user_type = 'center_user';
            $user->occupation = $request->occupation;
            $user->email = $request->email;
            $user->center_name = auth()->user()->center_name;
            $user->save();
            $user->syncRoles([$request->role]);
            Password::sendResetLink($request->only(['email']));
            return response()->json(['success' => 'Successfully Added new records.']);
        } else {
            return response()->json(['error' => ['number' => 'Sorry ,you are  only allowed to add only two users']]);
        }
    }

    public function editProfile()
    {
        return view('school.profile');
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
        $user->syncRoles([$request->role]);
        return redirect()->back()->with('success', 'successfuly updated the records');
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

        return redirect()->back()->with('success', 'password successfully changed');
    }

    public function getUserByUserName($userName)
    {
        $user = User::with('roles')->where('username', '=', $userName)->first();

        $profile = asset('adminAssets/assets/img/profile.png');

        if ($user->profile) {
            $profile = asset('uploads/profile/' . $user->profile);
        }

        return response()->json(['user' => $user, 'profile' => $profile]);
    }

    public function updateUser(Request $request)
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
        return response()->json(['success' => 'successfully updated records.']);
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
        User::where('username', '=', $request->userid)->update(['status' => $status]);
        return response()->json(['success' => $message]);
    }

    public function settings()
    {
        return view('school.settings');
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
}
