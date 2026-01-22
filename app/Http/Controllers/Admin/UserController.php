<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\Center;
use App\Models\District;
use App\Models\Role;
use App\Models\User;
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

        return response()->json(['status' => 1, 'message' => 'successfully updated records']);
    }

    public function destroy($id)
    {
        //
    }
}