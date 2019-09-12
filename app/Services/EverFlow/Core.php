<?php
namespace App\Services\EverFlow;

use Exception;

class Core {

    public function __construct()
    {

    }

    protected function curlGet($url, $info = false)
    {
        $header = [
            'x-eflow-api-key: '.config('services.everflow.api_key'),
        ];

        $urlFull = "https://api.eflow.team/v1" . $url;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlFull,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        if($info){ var_dump(curl_getinfo($curl)); }

        curl_close($curl);

        if ($err) {
            throw new Exception('Error: EverFlow ' . $err);
        } else {
            return json_decode($response);
        }
    }


    protected function curlPut($url, $param, $info = false)
    {
        $header = [
            'x-eflow-api-key: '.config('services.everflow.api_key'),
            'Content-Type: application/json',
        ];

        $urlFull = "https://api.eflow.team/v1" . $url;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlFull,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => json_encode($param),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        if($info){ var_dump(curl_getinfo($curl)); }

        curl_close($curl);

        if ($err) {
            throw new Exception('Error: EverFlow ' . $err);
        } else {
            return json_decode($response);
        }
    }


    protected function curlPatch($url, $param, $info = false)
    {
        $header = [
            'x-eflow-api-key: '.config('services.everflow.api_key'),
            'content-type: application/json',
        ];

        $urlFull = "https://api.eflow.team/v1" . $url;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlFull,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "PATCH",
            CURLOPT_POSTFIELDS => json_encode($param),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        if($info){ var_dump(curl_getinfo($curl)); }

        curl_close($curl);

        if ($err) {
            throw new Exception('Error: EverFlow ' . $err);
        } else {
            return json_decode($response);
        }
    }


    protected function curlPost($url, $param, $info = false)
    {
        $header = [
            'x-eflow-api-key: '.config('services.everflow.api_key'),
            'Content-Type: application/json',
        ];

        //var_dump(json_encode($param)); exit();

        $urlFull = "https://api.eflow.team/v1" . $url;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlFull,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POSTFIELDS => json_encode($param),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        if($info){ var_dump(curl_getinfo($curl)); }

        curl_close($curl);

        if ($err) {
            throw new Exception('Error: EverFlow ' . $err);
        } else {
            return json_decode($response);
        }
    }

}