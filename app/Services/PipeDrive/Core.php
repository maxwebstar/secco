<?php
namespace App\Services\PipeDrive;

use Exception;

class Core {


    const BASE_URL_V1 = 'https://api.pipedrive.com/v1/';
    const BASE_URL_COMPANY_V1 = 'https://seccosquared.pipedrive.com/v1/';


    private $api_token;


    public function __construct()
    {
        $this->api_token = config('services.pipedrive.api_token');
    }


    public function curlGet($url)
    {
        $url_ready = $url . $this->api_token;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url_ready);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);

        curl_close($ch);

        return json_decode($output, true);
    }


}