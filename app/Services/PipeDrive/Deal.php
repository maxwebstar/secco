<?php
namespace App\Services\PipeDrive;

use Exception;

class Deal extends Core
{


    public function __construct()
    {
        parent::__construct();
    }


    public function getAll()
    {
        $url = self::BASE_URL_COMPANY_V1 . "deals?start=200&limit=100&api_token=";
        $url = self::BASE_URL_COMPANY_V1 . "deals?api_token=";

        $result = $this->curlGet($url);

        dd($result);
    }


    public function getByID($id)
    {
        $url = self::BASE_URL_COMPANY_V1 . "deals/$id?api_token=";

        $result = $this->curlGet($url);

        dd($result);
    }



}