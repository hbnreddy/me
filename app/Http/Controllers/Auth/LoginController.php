<?php

namespace App\Http\Controllers\Auth;

use App\AppConfig;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    use AuthenticatesUsers;

    public function redirectPath()
    {
        return AppConfig::getLocale().'/';
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

    public function login(Request $request)
    {
        $this->validateLogin($request);

        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            $seconds = $this->limiter()->availableIn(
                $this->throttleKey($request)
            );

            $response = [
                'success' => false,
                'message' => "Too many attempts.",
                'wait_seconds' => $seconds,
            ];

            return redirect()->back();
        }

        if ($this->attemptLogin($request)) {
            $request->session()->regenerate();

            $this->clearLoginAttempts($request);

            $response = [
                'success' => true,
                'user' => Auth::user(),
            ];

            if (Auth::user()->admin) {
                return redirect()->route('admin.index');
            }

            return redirect()->back();
        }

        $this->incrementLoginAttempts($request);

        return redirect()->back();
    }

    public function auth()
    {
        return view('auth');
    }
}
