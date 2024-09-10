<?php

namespace App\Http\Controllers\Sponsor\Auth;

use App\Http\Controllers\Controller;
use App\Models\SponsorUser;
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
        if (!$tokenData) {
            //redirect them anywhere you want if the token does not exist.
            abort(403, 'The access token is expired or invalid.');
            // return redirect()->route('sponsor.login');
        } else {
            $email = $tokenData->email;
            return view('auth.sponsor.passwords.reset', compact('token', 'email'));
        }
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
        $user = SponsorUser::where('email', $tokenData->email)->first();
        if ($user) {
            $user->password = Hash::make($password);
            $user->update(); //or $user->save();
            DB::table('password_resets')->where('email', $user->email)->delete();
            if (Auth::guard('sponsor')->attempt(['email' =>   $tokenData->email, 'password' => $password])) {
                return redirect()->intended(route('sponsor.home'));
            }
        } else {
            abort(403, 'The access token is expired or invalid.');
        }
    }
}
