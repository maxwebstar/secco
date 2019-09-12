<?php
namespace App\Services\PipeDrive;

use Exception;

class Person extends Core
{


    public function __construct()
    {
        parent::__construct();
    }


    public function getByID($id)
    {
        $url = self::BASE_URL_COMPANY_V1 . "persons/$id?api_token=";

        $result = $this->curlGet($url);

        if($result['success']){
            return $result;
        } else {
            return false;
        }
    }

}