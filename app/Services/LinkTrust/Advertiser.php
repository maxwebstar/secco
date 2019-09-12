<?php
namespace App\Services\LinkTrust;

use Exception;
use App\Models\Advertiser as modelAdvertiser;

class Advertiser extends Core{

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Create new advertiser on LinkTrist
     *
     * @return int $advertiser_lt_id
     * @throws Exception Throws curl error if fails to create advertiser
     */
    public function createAdvertiser(modelAdvertiser $dataAdvertiser, $info = true)
    {
        $result = false;
        $data = array(
            "AutoApproveApplication" => 'True',
            "SuccessUrl" => "http://admin.seccosquared.com",
            "FailureUrl" => "http://admin.seccosquared.com",
            "ContactName" => $dataAdvertiser->name,
            "CompanyName" => $dataAdvertiser->contact,
            "ContactEmail" => $dataAdvertiser->email,
            "AddressLine1" => $dataAdvertiser->street1,
            "AddressLine2" => $dataAdvertiser->street2,
            "Province" => $dataAdvertiser->province,
            "PostalCode" => $dataAdvertiser->zip,
            "Country" => $dataAdvertiser->country,
            "City" => $dataAdvertiser->city,
            "State" => $dataAdvertiser->state,
            "Phone" => $dataAdvertiser->phone
        );

        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "http://merchant.seccosquared.com/Signup/Custom");
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_POST, count($data));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

            $response = curl_exec($ch);

            if($info){ var_dump(curl_getinfo($ch)); }

            curl_close($ch);

        } catch (Exception $e) {
            throw new Exception('Error: Curl failed with error ' . $e->getMessage());
        }

        if (strpos($response, 'MerchantId=') == TRUE) {

            $search = preg_match('/MerchantId=(.*?)">here/', $response, $matches);
            $advertiserId = (int) $matches[1];
            $result = $advertiserId;
        }

        return $result;
    }


    public function getStat($dateStart, $dateEnd, $filter = 'Traffic')
    {
        $params = array(
            'DateFrom' => date('n/j/Y', strtotime($dateStart)),
            'DateTo' => date('n/j/Y', strtotime($dateEnd)),
            'TrafficFilter' => $filter,
            'Status' => ''
        );

        $url = $this->createV1Link('/rest/'.$this->api_id.'/reports/merchantperformance.xml', $params);

        $result = $this->curlGet($url);
        $xml = simplexml_load_string($result);

        if(isset($xml->Merchant)){
            return $xml;
        } else {
            return false;
        }
    }

}