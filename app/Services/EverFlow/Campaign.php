<?php
namespace App\Services\EverFlow;


class Campaign extends Core
{

    public function __construct()
    {
        parent::__construct();
    }


    public function getCampaign($id)
    {
        $url = "/networks/campaigns/$id";

        $result = $this->curlGet($url);

        if(isset($result->network_campaign_id)){
            return $result;
        } else {
            return false;
        }
    }


    public function getAllCampaign()
    {
        $url = "/networks/campaigns";

        $result = $this->curlGet($url);

        if(isset($result->campaigns)){
            return $result;
        } else {
            return false;
        }
    }

}