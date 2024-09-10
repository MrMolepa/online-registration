<?php

namespace App\Http\Controllers\School\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    protected $username;
    public function index()
    {
        return view('auth.school.login'); //name of the admin login view
    }

    public function login(Request $request)
    {

        //Validate the Form Data
        $this->validate($request, [
            $this->username() => 'required|string',
            'password' => 'required|min:5'
        ]);

        //Attempt to log the Admin In
        $username = $request->username;
        $password = $request->password;
        $remember = $request->remember;
        //If Successful redirect to intended location
        if (Auth::guard()->attempt([$this->username() =>  $username, 'password' => $password], $remember)) {
            return redirect()->intended(route('center.home'));
        }

        //If Unsuccessful redirect back to login form with form data
        return redirect()->back()->withInput($request->only('username', 'remember'))->withErrors([
            'username' => "Credentials do not match records",
        ]);
    }
    public function logout(Request $request)
    {

        //logout the admin…
        Auth::guard()->logout();
        return redirect()->route('center.login');
    }

    public function username()
    {
        $loginType = request()->input('username');
        $this->username = filter_var($loginType, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$this->username => $loginType]);
        return  property_exists($this, 'username') ? $this->username : 'email';
    }
}
