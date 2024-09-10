<?php

namespace App\Http\Controllers\Candidate\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.candidate.login'); //name of the admin login view
    }

    public function login(Request $request)
    {
        //Validate the Form Data
        $this->validate($request, [
            'national_id' => 'required',
            'password' => 'required'
        ]);

        //Attempt to log the Admin In
        $national_id = $request->national_id;
        $password = $request->password;
        $remember = $request->remember;
        //If Successful redirect to intended location

        if (Auth::guard('candidate')->attempt(['national_id' =>   $national_id, 'password' => $password], $remember)) {
            return redirect()->intended(route('candidate.home'));
        }

        //If Unsuccessful redirect back to login form with form data
        return redirect()->back()->withInput($request->only('national_id', 'remember'))->withErrors([
            'national_id' => "Credentials do not match records",
        ]);;
    }
    public function logout(Request $request)
    {
        //logout the admin…
        Auth::guard('candidate')->logout();
        return redirect()->route('candidate.login');
    }
}
