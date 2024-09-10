<?php

namespace App\Http\Controllers\Sponsor\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.sponsor.login'); //name of the admin login view
    }

    public function login(Request $request)
    {


        //Validate the Form Data
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:5'
        ]);

        //Attempt to log the Admin In
        $email = $request->email;
        $password = $request->password;
        $remember = $request->remember;
        //If Successful redirect to intended location


        if (Auth::guard('sponsor')->attempt(['email' =>   $email, 'password' => $password], $remember)) {
            return redirect()->intended(route('sponsor.home'));
        }

        //If Unsuccessful redirect back to login form with form data
        return redirect()->back()->withInput($request->only('email', 'remember'));
    }
    public function logout(Request $request)
    {
        //logout the admin…
        Auth::guard('sponsor')->logout();
        return redirect()->route('sponsor.login');
    }
}
