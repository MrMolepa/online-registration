<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SponsorUser;
use App\Notifications\SendResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SponsorUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $tableSponsor = '<table class="table table-striped">
            <thead>
                <tr>
                   <th>State</th>
                   <th >Profile Picture</th>
                    <th>User Id</th>
                    <th>Sponsor Key</th>
                    <th>Level</th>
                    <th>Occupation</th>
                    <th>Email</th>
                    <th colspan="3">Action</th>
                </tr>
            </thead>';

            // `sponsor`, `level`,
            $users = SponsorUser::get();
            foreach ($users as $user) {
                $editurl=route('admin.sponsors.edit',$user->id);
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

                $tableSponsor .= '<tr>
                        <td><div class="' . $status . '"></div> </td>
                        <td class="profile-pic"><img src="' . $profile . '" alt="" width="40px"></td>
                        <td>' . $user->username . '</td>
                        <td>' . $user->sponsor . '</td>
                        <td>' . $user->level . '</td>
                        <td>' . $user->occupation . '</td>
                        <td>' . $user->email . '</td>
                        <td class="btns-action">
                            <div class="actions">
                                <a href="#" title="' . $user->status . '" class="change-status" data-user_status="' . $user->status . '" data-id="' . $user->id . '" > <i class="fas fa-signal  ' . $font_color . '"></i></a>
                                <a href="#" title="Edit" class="edit-sponsor"  data-action="' .   $editurl . '" > <i class="fas fa-edit"></i></a>
                            </div>
                        </td>
                </tr>';
            }
            return response()->json(['status'=>'success','tableSponsor' =>  $tableSponsor]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'occupation' => 'required',
            'email' => "required|unique:sponsors",
            'user_type' => 'required',
            'level' => 'required',
            'sponsor' => 'required',
            'districts' => ['required','array'],
            'districts.*'  => ["required","min:3","distinct"],
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
        $user->level = $request->level;
        $user->sponsor = $request->sponsor;
        $user->status = 1;
        $user->save();
        $user->districts()->sync($request->districts);
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

            $user->notify(new SendResetPassword(route('sponsor.password.reset', $token)));
        }
        return response()->json(['success' => 'Successfully add the records.']);

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
    public function edit($id)
    {
        $user =  SponsorUser::with('districts')->find($id);
        $url = route('admin.sponsors.update',$id );
        return response()->json(['user' => $user,'action'=>$url]);
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


        $validator = Validator::make($request->all(), [
            'occupation' => 'required',
            'email' => "required|unique:sponsors,email,$id",
            'user_type' => 'required',
            'level' => 'required',
            'sponsor' => 'required',
            'districts' => ['required','array'],
            'districts.*'  => ["required","min:3","distinct"],
        ]);



        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $userType = $request->user_type;
        $user =  $userType::find($id);
        $username  = strstr($request->email, '@', true);
        if ($request->hasFile("profileImage")) {
            $profile_pic = time() . '-' . $username . "." . $request->profileImage->extension();
            $request->profileImage->move(public_path('uploads/profile'), $profile_pic);
            $user->profile =  $profile_pic;
        }
        $user->username = $username;
        $user->occupation = $request->occupation;
        $user->email = $request->email;
        $user->level = $request->level;
        $user->sponsor = $request->sponsor;
        $user->status = 1;
        $user->save();
        $user->districts()->sync($request->districts);
        return response()->json(['success' => 'Successfully updated the records.']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
