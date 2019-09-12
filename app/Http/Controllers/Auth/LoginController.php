<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

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

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function redirectTo()
    {
        if(Auth::check()) {
            if (Auth::user()->getRoleFirstPriority('priority') > 19) {
                return route('admin.dashboard');
            }
        }

        return route('home');
    }

    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();
        if($user && Hash::check($request->password, $user->password)){
            switch($user->status){
                case 0 :
                    throw ValidationException::withMessages([
                        $this->username() => ["These credentials do not match our records."],
                    ]);
                    break;
                case 1 :
                    throw ValidationException::withMessages([
                        $this->username() => ["User $request->email is not approved."],
                    ]);
                    break;
                case 2 :
                    throw ValidationException::withMessages([
                        $this->username() => ["User $request->email is rejected."],
                    ]);
                    break;
                default :
                    break;
            }
        }
    }
}
