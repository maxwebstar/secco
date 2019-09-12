<?php
namespace App\Services\EverFlow;

use Exception;

class Creative extends Core
{

    public function __construct()
    {
        parent::__construct();
    }


    public function getOffer($id)
    {
        $url = "/networks/creatives/$id";

        $result = $this->curlGet($url);

        if (isset($result->network_offer_creative_id)) {
            return $result;
        } else {
            return false;
        }
    }


    public function getAllCreative($page = 1, $page_size = 100)
    {
        $url = "/networks/creatives?page=$page&page_size=$page_size";

        $result = $this->curlGet($url);

        if (isset($result->creatives)) {
            return $result;
        } else {
            return false;
        }
    }

}