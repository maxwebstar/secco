<?php
namespace App\Services\PipeDrive;

use Exception;

class Organization extends Core
{


    public function __construct()
    {
        parent::__construct();
    }


    public function getByID($id)
    {
        $url = self::BASE_URL_COMPANY_V1 . "organizations/$id?api_token=";

        $result = $this->curlGet($url);

        if($result['success']){
            return $result;
        } else {
            return false;
        }
    }


    public function getAll($start = 0, $limit = 0)
    {
        if($start && $limit){
            $url = self::BASE_URL_COMPANY_V1 . "organizations?start=$start&limit=$limit&api_token=";
        } else {
            $url = self::BASE_URL_COMPANY_V1 . "organizations?api_token=";
        }

        $result = $this->curlGet($url);

        if($result['success']){
            return $result;
        } else {
            return false;
        }
    }

}