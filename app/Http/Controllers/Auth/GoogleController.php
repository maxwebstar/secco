<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;


use App\Models\User;
use Socialite;
use Exeption;
use App\Services\Google;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirect()
    {
        /*return Socialite::driver('google')
            ->scopes(config('services.google.scope'))
            ->with(["access_type" => "offline", "prompt" => "consent select_account"])
            ->redirect();*/

        $google = new Google();
        $url = $google->getAuthUrl();

        return redirect($url);
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function callback(Request $request)
    {
        /*$user = Socialite::driver('google')->user();*/

        if(empty($request->code)){
            throw new Exception('Error: Google AuthCode not exist.');
        }

        $google = new Google();
        $modelUser = new User();

        $token = $google->createAccessToken($request->code);
        $user = $google->getUser();

        if($user){

            $seccoUser = $modelUser->where('google_id', $user->id)->first();
            if($seccoUser){
                $seccoUser->setGoogleToken($token);
            } else {
                $seccoUser = $modelUser->where('email', $user->email)->first();
                if($seccoUser){
                    $seccoUser->google_id = $user->id;
                    $seccoUser->setGoogleToken($token);
                }
            }

            if(!$seccoUser){

                $case = 'new_user';
                $password = str_random(10);

                $seccoUser = $modelUser;
                $seccoUser->fill([
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => Hash::make($password),
                    'status' => 1,
                    'google_id' => $user->id,
                    'google_token' => json_encode($token),
                ]);
            }

            if(isset($token['refresh_token'])){
                $seccoUser->google_refresh_token = $token['refresh_token'];
            }
            if(empty($seccoUser->google_refresh_token)){
                $url = $google->refreshAccessForGetRefreshToken();
                return $this->redirect($url);
            }

            $seccoUser->save();

            switch($seccoUser->status){
                case 0 :
                    return redirect()->route('login')->withErrors(['email' => "These credentials do not match our records."]);
                    break;
                case 1:
                    if(isset($case) && $case == 'new_user'){
                        return redirect()->route('home')->with(['message' => [
                            'type' => 'success',
                            'title' => 'Success!',
                            'message' => "Congratulations, your account has been created. Please wait while admin checks your account.",
                            'autohide' => 1,
                        ]]);
                    } else {
                        return redirect()->route('login')->withErrors(['email' => "User $seccoUser->email is not approved"]);
                    }
                    break;
                case 2:
                    return redirect()->route('login')->withErrors(['email' => "User $seccoUser->email is not approved"]);
                    break;
                case 3:
                    Auth::guard()->login($seccoUser);

                    $rolePriority = $seccoUser->getRoleFirstPriority('priority');
                    if($rolePriority > 19){
                        return redirect()->route('admin.dashboard');
                    }
                    break;
                default:
                    break;
            }

            return redirect()->route('home');

        } else {
            return abort(500);
        }
    }
}
