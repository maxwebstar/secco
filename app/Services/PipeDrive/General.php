<?php
namespace App\Services\PipeDrive;

use Exception;

class General extends Core {


    public function __construct()
    {
        parent::__construct();
    }


    public function getMe()
    {
        $url = self::BASE_URL_V1 . "users/me?api_token=";

        $result = $this->curlGet($url);

        dd($result);
    }


    public function getUser($id)
    {

    }


    public function getAllUser()
    {
        $url = self::BASE_URL_V1 . "users?api_token=";

        $result = $this->curlGet($url);

        if(isset($result['success']) && $result['success']){
            return $result['data'];
        } else {
            return false;
        }
    }


    public function getAllRole()
    {
        $url = self::BASE_URL_V1 . "roles?api_token=";

        $result = $this->curlGet($url);

        dd($result);
    }
}