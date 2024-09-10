<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    // use AuthenticatesUsers;
    use AuthenticatesUsers {
        logout as performLogout;
    }

    protected $username;


    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function redirectTo()
    {

        $user_type = auth()->user()->user_type;

        switch ($user_type) {
            case 'ECoL_admin':
                $this->redirectTo = route('admin.home');
                return  $this->redirectTo;
                break;
            case 'center':
                $this->redirectTo = '/center';
                return  $this->redirectTo;

                break;
            case 'center_user':
                $this->redirectTo = '/center';
                return  $this->redirectTo;
                break;
            case 'candidate':
                $this->redirectTo = '/candidate';
                return  $this->redirectTo;
                break;
            default:
                $this->redirectTo = '/login';
                return  $this->redirectTo;
                break;
        }
    }
    /**
     * Where to redirect users after logout.
     *
     * @var string
     */
    public function logout(Request $request)
    {
        $this->performLogout($request);
        return redirect()->route('login');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        $loginType = request()->input('username');
        $this->username = filter_var($loginType, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$this->username => $loginType]);
        return  property_exists($this, 'username') ? $this->username : 'email';
    }
}
