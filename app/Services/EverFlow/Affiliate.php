<?php
namespace App\Services\EverFlow;


class Affiliate extends Core
{

    public function __construct()
    {
        parent::__construct();
    }


    public function getAffiliate($id)
    {
        $url = "/networks/affiliates/$id";

        $result = $this->curlGet($url);

        if(isset($result->network_affiliate_id)){
            return $result;
        } else {
            return false;
        }
    }


    public function getAllAffiliate($page = 1, $page_size = 100)
    {
        $url = "/networks/affiliates?page=$page&page_size=$page_size";

        $result = $this->curlGet($url);

        if(isset($result->affiliates)){
            return $result;
        } else {
            return false;
        }
    }


    public function getAllStat($dateStart, $dateEnd)
    {
        $url = "/networks/reporting/entity";

        $param = [
            'currency_id' => "USD",
            'from' => date('Y-m-d', strtotime($dateStart)),
            'to' => date('Y-m-d', strtotime($dateEnd)),
            'timezone_id' => 80, /*America/New_York*/
        ];
        $param['columns'][] = ['column' => 'offer'];
        $param['columns'][] = ['column' => 'affiliate'];
        //$param['columns'][] = ['column' => 'date'];

        $result = $this->curlPost($url, $param);

        if(isset($result->performance)){
            return $result;
        } else {
            return false;
        }
    }
}