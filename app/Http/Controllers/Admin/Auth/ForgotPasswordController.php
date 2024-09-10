<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Notifications\SendResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ForgotPasswordController extends Controller
{
    //
    public function showLinkRequestForm()
    {
        return view('auth.admin.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $user = AdminUser::whereEmail($request->email)->first();
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
        return redirect()->back()->with('success', 'IT WORKS!');
    }
}
