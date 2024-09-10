<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function showResetForm($token)
    {
        $tokenData = DB::table('password_resets')
            ->where('token', $token)->first();
        if (!$tokenData) return redirect()->to('home');
        //redirect them anywhere you want if the token does not exist.
        $email = $tokenData->email;
        return view('auth.admin.passwords.reset', compact('token', 'email'));
    }

    public function reset(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6'
        ]);


        //some validation
        $password = $request->password;
        $tokenData = DB::table('password_resets')
            ->where('token', $request->token)->first();
        $user = AdminUser::where('email', $tokenData->email)->first();
        if (!$user) return redirect()->to('admin'); //or wherever you want
        $user->password = Hash::make($password);
        $user->update(); //or $user->save();
        //do we log the user directly or let them login and try their password for the first time ? if yes
        Auth::login($user);
        // If the user shouldn't reuse the token later, delete the token
        DB::table('password_resets')->where('email', $user->email)->delete();
        return redirect()->intended(route('admin.home'));
    }
}
