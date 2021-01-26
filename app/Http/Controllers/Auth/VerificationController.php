<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

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
    protected $redirectTo = '/email-verified';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verified()
    {
        return view('auth.email-verified');
    }

    public function verify(Request $request)
    {
        $user = User::query()->find($request->route('id'));

        $user->markEmailAsVerified();

        return redirect()->to($this->redirectPath());
    }

    public function emailConfirmationSent()
    {
        return view('auth.email-confirmation-sent');
    }

    public function notActivatedAccount()
    {
        return view('auth.account-not-activated');
    }
}
