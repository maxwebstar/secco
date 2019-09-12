<?php
namespace App\Services\LinkTrust;

use Exception;

class Affiliate extends Core
{

    public function __construct()
    {
        parent::__construct();
    }


    public function getAllAffiliate()
    {
        $url = "https://api.linktrust.com/v2/affiliate";

        $result = $this->curlGet($url);
        $json = json_decode($result);

        if(is_array($json) && isset($json[0]->AffiliateId)){
            return $json;
        } else {
            return false;
        }
    }


    public function getStat($dateStart, $dateEnd, $filter = 'Traffic')
    {
        $params = array(
            'DateFrom' => $dateStart,
            'DateTo' => $dateEnd,
            'TrafficFilter' => $filter,
            'Status' => ''
        );

        $url = $this->createV1Link('/rest/'.$this->api_id.'/reports/affiliateperformance.xml', $params);

        $result = $this->curlGet($url);
        $xml = simplexml_load_string($result);

        if($xml->Affiliate){
            return $xml;
        } else {
            return false;
        }
    }

}