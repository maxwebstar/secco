<?php
namespace App\Services;

use Google_Client;
use Google_Service_Oauth2;

use Google_Service_Drive;
use Google_Service_Drive_DriveFile;

use Illuminate\Support\Facades\Auth;
use Exception;

class Google
{

    protected $google_client;

    public function __construct()
    {
        $googleClient = new Google_Client();

        $googleClient->setClientId(config('services.google.client_id'));
        $googleClient->setClientSecret(config('services.google.client_secret'));
        $googleClient->setRedirectUri(config('services.google.redirect'));
        $googleClient->setScopes(config('services.google.scope'));
        $googleClient->setAccessType('offline');

        $this->google_client = $googleClient;
    }

    public function checkAccessToken()
    {
        $isAuth = Auth::check();

        $token = $this->google_client->getAccessToken();
        if(!$token && $isAuth){
            $user = Auth::user();
            $this->google_client->setAccessToken($user->google_token);
        }

        if ($this->google_client->isAccessTokenExpired() && $isAuth) {
            if(isset($user) == false){
                $user = Auth::user();
            }
            $token = $this->google_client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);

            if($token && empty($token['error'])){
                $user->setGoogleToken($token);
                $user->save();
            }
        } else {
            $token = $this->google_client->getAccessToken();
        }

        if(empty($token)){
            throw new Exception('Error: Google AccessToken is empty.');
        }
    }

    public function getClient()
    {
        return $this->google_client;
    }

    public function getAuthUrl()
    {
        return $this->google_client->createAuthUrl();
    }

    public function createAccessToken($code)
    {
        if(is_string($code) == false){
            throw new Exception('Error: Google AuthCode not valid');
        }

        $accessCode = $this->google_client->fetchAccessTokenWithAuthCode($code);
        if($accessCode){
            return $accessCode;
        } else {
            return false;
        }
    }

    public function getUser()
    {
        $this->checkAccessToken();

        $googleOauth = new Google_Service_Oauth2($this->google_client);
        return $googleOauth->userinfo->get();
    }

    public function refreshAccessForGetRefreshToken()
    {
        $this->google_client->revokeToken();

        return $this->getAuthUrl();
    }

}