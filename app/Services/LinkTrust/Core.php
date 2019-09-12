<?php
namespace App\Services\LinkTrust;

use Exception;

class Core {


    const BASE_URL_V1 = 'http://integration.beta.linktrust.com';
    const BASE_URL_V2 = 'https://api.linktrust.com/v2';

    protected $api_id;
    private $api_key;
    private $api_user;
    private $api_pass;

    private $api_credential;

    public function __construct()
    {
        $this->api_id = config('services.linktrust.id');
        $this->api_key = config('services.linktrust.key');
        $this->api_user = config('services.linktrust.user');
        $this->api_pass = config('services.linktrust.pass');

        $this->api_credential = "$this->api_user:$this->api_pass";
    }


    private function getToken($endpoint)
    {
        $date = gmdate('YmdHi');
        $hash = md5($endpoint.$date.$this->api_key);
        $hash = strtoupper($hash);
        $token = $date."_".$hash;

        return $token;
    }


    protected function curlGet($url)
    {
        if (!isset($url) || empty($url)) {
            throw new Exception('Linktrust Endpoint URL is missing, enter valid endpoint to call LT api.');
        }

        try {
            // Get cURL resource
            $curl = curl_init();

            // Set some options - we are passing in a useragent too here

            curl_setopt_array($curl, array(
                CURLOPT_URL        => $url,
                CURLOPT_HTTPAUTH   => CURLAUTH_BASIC,
                CURLOPT_USERPWD    => $this->api_credential,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_RETURNTRANSFER => 1,
            ));

            // Send the request & save response to $resp
            $response = curl_exec($curl);
            // Close request to clear up some resources
            curl_close($curl);

            if (FALSE === $response)
                throw new Exception(curl_error($curl), curl_errno($curl));

        } catch (Exception $e) {

            trigger_error(sprintf('Curl failed with error #%d: %s', $e->getCode(), $e->getMessage()), E_USER_ERROR);
        }

        return $response;
    }


    protected function processXMLRequest($url, $payload){

        $header = array("Content-type: application/xml");

        try {
            // Get cURL resource
            $curl = curl_init();

            // Set some options - we are passing in a useragent too here
            curl_setopt_array($curl, array(

                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $url,
                    CURLOPT_SSL_VERIFYPEER => FALSE,
                    CURLOPT_SSL_VERIFYHOST => FALSE,
                    CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                    CURLOPT_USERPWD => $this->api_credential,
                    CURLOPT_HTTPHEADER => $header,
                    CURLOPT_CUSTOMREQUEST => "PUT",
                    CURLOPT_POSTFIELDS => "$payload"
                )
            );

            // Send the request & save response to $resp
            $response = curl_exec($curl);
            // Close request to clear up some resources
            curl_close($curl);

            if (FALSE === $response)
                throw new Exception(curl_error($curl), curl_errno($curl));

        } catch (Exception $e) {

            trigger_error(sprintf('Curl failed with error #%d: %s', $e->getCode(), $e->getMessage()), E_USER_ERROR);
        }

        return $response;
    }


    protected function createV1Link($endpoint, array $params)
    {
        $params['token'] = $this->getToken($endpoint);

        $url = SELF::BASE_URL_V1 . $endpoint.'?'. http_build_query($params);

        $url = str_replace('%2F', '/', $url);

        return $url;
    }


    protected function createV2Link($endpoint, array $params)
    {
        $params['token'] = $this->getToken($endpoint);

        $url = SELF::BASE_URL_V2 . $endpoint.'?'. http_build_query($params);

        $url = str_replace('%2F', '/', $url);

        return $url;
    }

}