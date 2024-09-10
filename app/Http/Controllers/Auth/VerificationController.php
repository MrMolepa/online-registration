<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
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

            default:
                $this->redirectTo = '/login';
                return  $this->redirectTo;
                break;
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }
}
