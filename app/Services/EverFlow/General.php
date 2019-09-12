<?php

namespace App\Services\EverFlow;

class General extends Core {

    public function __construct()
    {
        parent::__construct();
    }


    public function getAllCountry()
    {
        $url = "/meta/countries";

        $result = $this->curlGet($url);

        if(isset($result->countries)){
            return $result->countries;
        } else {
            return false;
        }
    }

    public function getAllRegion()
    {
        $url = "/meta/regions";

        $result = $this->curlGet($url);

        if(isset($result->regions)){
            return $result->regions;
        } else {
            return false;
        }
    }

    public function getAllCategory()
    {
        $url = "/networks/categories";

        $result = $this->curlGet($url);

        if(isset($result->categories)){
            return $result->categories;
        } else {
            return false;
        }
    }

    public function getAllDomain()
    {
        $url = "/networks/domains/tracking";

        $result = $this->curlGet($url);

        if(isset($result->domains)){
            return $result->domains;
        } else {
            return false;
        }
    }

    public function getAllTimezone()
    {
        $url = "/meta/timezones";

        $result = $this->curlGet($url);

        if(isset($result->timezones)){
            return $result->timezones;
        } else {
            return false;
        }
    }

    public function loadAffiliate()
    {
        $page = 1;
        $page_size = 100;

        $ef_Affiliate = new \App\Services\EverFlow\Affiliate();

        $efResponse = $ef_Affiliate->getAllAffiliate($page, $page_size);

        if($efResponse->affiliates){

            $this->cycleAffiliates($efResponse->affiliates);

            $total = $efResponse->paging->total_count;
            if($total > $page_size){
                $page_last = ceil(($total/$page_size));
            } else {
                $page_last = 1;
            }

            while($page_last > $page){
                $page ++;
                $efResponse = $ef_Affiliate->getAllAffiliate($page, $page_size);

                if($efResponse->affiliates){
                    $this->cycleAffiliates($efResponse->affiliates);
                }
            }
        }

    }


    public function loadOffer()
    {
        $page = 1;
        $page_size = 100;

        $ef_Offer = new \App\Services\EverFlow\Offer();

        $efResponse = $ef_Offer->getAllOffer($page, $page_size);

        if($efResponse->offers){

            $this->cycleOffer($efResponse->offers);

            $total = $efResponse->paging->total_count;
            if($total > $page_size){
                $page_last = ceil(($total/$page_size));
            } else {
                $page_last = 1;
            }

            while($page_last > $page){
                $page ++;
                $efResponse = $ef_Offer->getAllOffer($page, $page_size);

                if($efResponse->offers){
                    $this->cycleOffer($efResponse->offers);
                }
            }
        }

    }


    protected function cycleAffiliates($data)
    {
        $model = new \App\Models\Tmp\EF\Affiliate();

        foreach($data as $iterAffilate){

            $model->saveData($iterAffilate);
        }
    }


    protected function cycleOffer($data)
    {
        $model = new \App\Models\Tmp\EF\Offer();

        foreach($data as $iterOffer){

            $model->saveData($iterOffer);
        }
    }

}