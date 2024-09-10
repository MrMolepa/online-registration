<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    protected $username;
    public function index()
    {

        return view('auth.admin.login'); //name of the admin login view
    }

    public function login(Request $request)
    {

        //Validate the Form Data
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|min:5'
        ]);
        //Attempt to log the Admin In
        $username = $request->email;
        $password = $request->password;
        $remember = $request->remember;

        //If    Successful redirect to intended location
        if (Auth::guard('admin')->attempt(['email' => $username, 'password' => $password], $remember)) {
            return redirect()->intended(route('admin.home'));
        }

       //If Unsuccessful redirect back to login form with form data
        return redirect()->back()->withInput($request->only('username', 'remember'));
    }

    public function logout(Request $request)
    {
        //logout the admin…
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    public function username()
    {
        $loginType = request()->input('username');
        $this->username = filter_var($loginType, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$this->username => $loginType]);
        return  property_exists($this, 'username') ? $this->username : 'email';
    }


}
