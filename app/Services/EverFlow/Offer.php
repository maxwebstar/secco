<?php
namespace App\Services\EverFlow;

use App\Models\Offer as modelOffer;
use App\Models\Request\Cap as modelRequestCap;
use App\Models\Request\Status as modelRequestStatus;
use App\Models\Request\Price as modelRequestPrice;

use Exception;

class Offer extends Core
{

    public function __construct()
    {
        parent::__construct();
    }


    public function getOffer($id, $relationship = false)
    {
        if($relationship){
            $url = "/networks/offers/$id?relationship=$relationship";
        } else {
            $url = "/networks/offers/$id";
        }

        $result = $this->curlGet($url);

        if (isset($result->network_offer_id)) {
            return $result;
        } else {
            return false;
        }
    }


    public function getAllOffer($page = 1, $page_size = 100)
    {
        $url = "/networks/offers?page=$page&page_size=$page_size";

        $result = $this->curlGet($url);

        if (isset($result->offers)) {
            return $result;
        } else {
            return false;
        }
    }


    public function getStat($dateStart, $dateEnd)
    {
        $url = "/networks/reporting/entity";

        $param = [
            'currency_id' => "USD",
            'from' => date('Y-m-d', strtotime($dateStart)),
            'to' => date('Y-m-d', strtotime($dateEnd)),
            'timezone_id' => 80, /*America/New_York*/
        ];
        $param['columns'][] = ['column' => 'affiliate'];
        $param['columns'][] = ['column' => 'date'];

        $result = $this->curlPost($url, $param);

        if (isset($result->performance)) {
            return $result;
        } else {
            return false;
        }
    }


    public function getStatByAffiliate($dateStart, $dateEnd)
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

        $result = $this->curlPost($url, $param);

        if (isset($result->performance)) {
            return $result;
        } else {
            return false;
        }
    }


    public function createOffer(modelOffer $data)
    {
        $url = "/networks/offers";

        $campaignType = $data->campaign_type_param->ef_key ? : 'blank';
        $revenueType = [
            'blank' => 'blank',
            'cpc' => 'rpc',
            'cpa' => 'rpa',
            'cpm' => 'rpm',
            'cps' => 'rps',
            'cpa_cps' => 'rpa_rps',
        ];

        $advertiser = $data->advertiser;
        if($advertiser->currency_id == 2 || $advertiser->currency_id == 3){

            $currency = $advertiser->currency;

            $price_in = round(($data->price_in * $currency->rate), 2);
            $price_out = round(($data->price_out * $currency->rate), 2);

        } else {
            $price_in = $data->price_in;
            $price_out = $data->price_out;
        }

        $param = [
            'name' => $data->campaign_name,
            'currency_id' => "USD",
            'network_advertiser_id' => $advertiser->ef_id,
            'offer_status' => $data->ef_status,
            'destination_url' => $data->campaign_link,
            'html_description' => $data->pixel_location . ' ' . $data->accepted_traffic . ' ' . $data->affiliate_note,
            'internal_notes' => $data->internal_note ?: "",
            'conversion_method' => $data->pixel->ef_key, /*http_image_pixel, https_image_pixel, server_postback, cookie_based, http_iframe_pixel, https_iframe_pixel, javascript*/
            'network_tracking_domain_id' => $data->domain->ef_id, /*деяких значень можу не бути*/
            'session_definition' => 'cookie', /*cookie, ip, ip_user_agent*/
            'session_duration' => 720, /*integer*/
            'redirect_mode' => 'standard', /*standard, single_meta_refresh, double_meta_refresh*/
            'visibility' => 'require_approval', /*public, require_approval, private*/

            "labels" => [],
            "is_use_secure_link" => false,
            "duplicate_filter_targeting_action" => "fail_traffic", /*block, fail_traffic*/

            "redirect_routing_method" => "internal",
            "redirect_internal_routing_type" => "priority",

            "is_fail_traffic_enabled" => false,
            "creatives" => [],
            "is_allow_deep_link" => false,
            "is_using_explicit_terms_and_conditions" => false,
            "is_using_suppression_list" => false,
            "is_view_through_enabled" => false,
            "is_duplicate_filter_enabled" => false,
            "is_must_approve_conversion" => false,
            "is_seo_friendly" => false,
            "is_allow_duplicate_conversion" => false,
            "is_session_tracking_enabled" => false,
            "is_use_scrub_rate" => false,

        ];

        $category_ef_id = $data->offer_category->ef_id;
        if($category_ef_id){
            $param['network_category_id'] = $category_ef_id;
        }

        $param['payout_revenue'][] = [
            "is_default" => true,
            //"is_private" => false,
            'entry_name' => 'Base',
            'payout_type' => $campaignType, /*blank, cpc, cpa, cpm, cps, cpa_cps, prv, null_value*/
            //'payout_percentage', /*if payout_type = cpa_cps*/
            'payout_amount' => floatval($price_out),
            'revenue_type' => isset($revenueType[$campaignType]) ? $revenueType[$campaignType] : "blank", /*blank, rpc, rpa, rpm, rps, rpa_rps*/
            //'revenue_percentage', /*if revenue_type = rpa_rps*/
            'revenue_amount' => floatval($price_in),
        ];


        if ($data->cap_type_id || $data->cap_unit_id) {

            $capType = $data->cap_type;
            $capUnit = $data->cap_unit;

            $param['is_caps_enabled'] = true;

            switch ($capType->key) {
                case "perday" :

                    $param["daily_click_cap"] = 0;
                    $param["daily_conversion_cap"] = 0;
                    $param["daily_payout_cap"] = 0;
                    $param["daily_revenue_cap"] = 0;

                    if ($capUnit->key == "lead") {
                        $param["daily_conversion_cap"] = (int) $data->cap_lead;
                    } elseif ($capUnit->key == "monetary") {
                        $param["daily_revenue_cap"] = floatval($data->cap_monetary);
                    }

                    break;
                case "perweek" :

                    $param["weekly_click_cap"] = 0;
                    $param["weekly_conversion_cap"] = 0;
                    $param["weekly_payout_cap"] = 0;
                    $param["weekly_revenue_cap"] = 0;

                    if ($capUnit->key == "lead") {
                        $param["weekly_conversion_cap"] = (int) $data->cap_lead;
                    } elseif ($capUnit->key == "monetary") {
                        $param["weekly_revenue_cap"] = floatval($data->cap_monetary);
                    }

                    break;
                case "permonth" :

                    $param["monthly_click_cap"] = 0;
                    $param["monthly_conversion_cap"] = 0;
                    $param["monthly_payout_cap"] = 0;
                    $param["monthly_revenue_cap"] = 0;

                    if ($capUnit->key == "lead") {
                        $param["monthly_conversion_cap"] = (int) $data->cap_lead;
                    } elseif ($capUnit->key == "monetary") {
                        $param["monthly_revenue_cap"] = floatval($data->cap_monetary);
                    }

                    break;
                case "total" :

                    $param["global_click_cap"] = 0;
                    $param["global_conversion_cap"] = 0;
                    $param["global_payout_cap"] = 0;
                    $param["global_revenue_cap"] = 0;

                    if ($capUnit->key == "lead") {
                        $param["global_conversion_cap"] = (int) $data->cap_lead;
                    } elseif ($capUnit->key == "monetary") {
                        $param["global_revenue_cap"] = floatval($data->cap_monetary);
                    }

                    break;
            }

        } else {

            $param['is_caps_enabled'] = false;

            $param["daily_click_cap"] = 0;
            $param["daily_conversion_cap"] = 0;
            $param["daily_payout_cap"] = 0;
            $param["daily_revenue_cap"] = 0;

            $param["global_click_cap"] = 0;
            $param["global_conversion_cap"] = 0;
            $param["global_payout_cap"] = 0;
            $param["global_revenue_cap"] = 0;

            $param["weekly_click_cap"] = 0;
            $param["weekly_conversion_cap"] = 0;
            $param["weekly_payout_cap"] = 0;
            $param["weekly_revenue_cap"] = 0;

            $param["monthly_click_cap"] = 0;
            $param["monthly_conversion_cap"] = 0;
            $param["monthly_payout_cap"] = 0;
            $param["monthly_revenue_cap"] = 0;
        }


        $dataGeos = $data->getGeos();
        if ($dataGeos) {

            $param['ruleset'] = [
                "languages" => [],
                "device_makes" => [],
                "browsers" => [],
                "device_types" => [],
                "platforms" => [],
                "os_versions" => [],
                "regions" => [],
                "cities" => [],
                "dmas" => [],
                "mobile_carriers" => [],
                "ips" => [],
                "connection_types" => [],
                "is_use_day_parting" => false,
                "day_parting_apply_to" => "null_value",
                "day_parting_timezone_id" => 0,
                "is_block_proxy" => false,
            ];

            foreach ($dataGeos as $geo) {
                if ($geo->ef_id) {
                    $param['ruleset']['countries'][] = [
                        'country_id' => $geo->ef_id,
                        'targeting_type' => 'include', /*include, exclude*/
                        'match_type' => 'exact', /*exact, minimum, maximum, contains, starts_with, range, ends_with, blank, does_not_match, does_not_contain*/
                    ];
                }
            }
        }

        $result = $this->curlPost($url, $param);

        if (isset($result->network_offer_id)) {
            return ['ef_id' => $result->network_offer_id];
        } else {
            $message = isset($result->Error) ? $result->Error : $result->error;
            return ['ef_id' => 0, 'message' => $message];
        }
    }


    public function updateLiteOffer(modelOffer $data)
    {
        if(!$data->ef_id){
            throw new Exception('Empty offer EverFlow id.');
        }

        $url = "/networks/offerstyped";

        $field = [];
        /*$field[] = [
            "field_type" => "name",
            "field_value" => $data->campaign_name,
        ];*/
        $field[] = [
            "field_type" => "offer_status",
            "field_value" => $data->ef_status,
        ];
        /*$field[] = [
            "field_type" => "destination_url",
            "field_value" => $data->campaign_link,
        ];*/
        $field[] = [
            "field_type" => "html_description",
            "field_value" => $data->pixel_location . ' ' . $data->accepted_traffic . ' ' . $data->affiliate_note,
        ];
        $field[] = [
            "field_type" => "internal_notes",
            "field_value" => $data->internal_note ? : "",
        ];
        $field[] = [
            "field_type" => "conversion_method",
            "field_value" => $data->pixel->ef_key,
        ];
        $field[] = [
            "field_type" => "network_tracking_domain_id",
            "field_value" => $data->domain->ef_id,
        ];

        $category_ef_id = $data->offer_category->ef_id;
        if($category_ef_id){
            $field[] = [
                "field_type" => "network_category_id",
                "field_value" => $category_ef_id,
            ];
        }

        $param['network_offer_ids'] = [$data->ef_id];
        $param['fields'] = $field;

        $result = $this->curlPatch($url, $param);

        if (isset($result->result)) {
            return ['updated' => $result->result];
        } else {
            $message = isset($result->Error) ? $result->Error : $result->error;
            return ['updated' => 0, 'message' => $message];
        }
    }


    public function updateOfferCap(modelOffer $data, modelRequestCap $cap)
    {
        if(!$data->ef_id){
            throw new Exception('Empty offer EverFlow id.');
        }

        $url = "/networks/offerstyped";

        $param['network_offer_ids'] = [$data->ef_id];

        if ($data->cap_type_id) {

            $capType = $cap->cap_type;
            $capUnit = $data->cap_unit;

            $field[] = [
                "field_type" => "is_caps_enabled",
                "field_value" => true,
            ];

            switch ($capType->key) {
                case "perday" :

                    $field[] = [
                        "field_type" => "daily_click_cap",
                        "field_value" => 0,
                    ];
                    $field[] = [
                        "field_type" => "daily_conversion_cap",
                        "field_value" => 0,
                    ];
                    $field[] = [
                        "field_type" => "daily_payout_cap",
                        "field_value" => 0,
                    ];
                    $field[] = [
                        "field_type" => "daily_revenue_cap",
                        "field_value" => 0,
                    ];

                    if ($capUnit->key == "lead") {
                        $field[] = [
                            "field_type" => "daily_conversion_cap",
                            "field_value" => (int) $cap->cap,
                        ];
                    }

                    break;
                case "perweek" :

                    $field[] = [
                        "field_type" => "weekly_click_cap",
                        "field_value" => 0,
                    ];
                    $field[] = [
                        "field_type" => "weekly_conversion_cap",
                        "field_value" => 0,
                    ];
                    $field[] = [
                        "field_type" => "weekly_payout_cap",
                        "field_value" => 0,
                    ];
                    $field[] = [
                        "field_type" => "weekly_revenue_cap",
                        "field_value" => 0,
                    ];

                    if ($capUnit->key == "lead") {
                        $field[] = [
                            "field_type" => "weekly_conversion_cap",
                            "field_value" => (int) $cap->cap,
                        ];
                    }

                    break;
                case "permonth" :

                    $field[] = [
                        "field_type" => "monthly_click_cap",
                        "field_value" => 0,
                    ];
                    $field[] = [
                        "field_type" => "monthly_conversion_cap",
                        "field_value" => 0,
                    ];
                    $field[] = [
                        "field_type" => "monthly_payout_cap",
                        "field_value" => 0,
                    ];
                    $field[] = [
                        "field_type" => "monthly_revenue_cap",
                        "field_value" => 0,
                    ];

                    if ($capUnit->key == "lead") {
                        $field[] = [
                            "field_type" => "monthly_conversion_cap",
                            "field_value" => (int) $cap->cap,
                        ];
                    }

                    break;
                case "total" :

                    $field[] = [
                        "field_type" => "global_click_cap",
                        "field_value" => 0,
                    ];
                    $field[] = [
                        "field_type" => "global_conversion_cap",
                        "field_value" => 0,
                    ];
                    $field[] = [
                        "field_type" => "global_payout_cap",
                        "field_value" => 0,
                    ];
                    $field[] = [
                        "field_type" => "global_revenue_cap",
                        "field_value" => 0,
                    ];

                    if ($capUnit->key == "lead") {
                        $field[] = [
                            "field_type" => "global_conversion_cap",
                            "field_value" => (int) $cap->cap,
                        ];
                    }

                    break;
            }
        }

        if($cap->cap_reset) {

            $field[] = [
                "field_type" => "is_caps_enabled",
                "field_value" => false,
            ];

            $field[] = [
                "field_type" => "daily_click_cap",
                "field_value" => 0,
            ];
            $field[] = [
                "field_type" => "daily_conversion_cap",
                "field_value" => 0,
            ];
            $field[] = [
                "field_type" => "daily_payout_cap",
                "field_value" => 0,
            ];
            $field[] = [
                "field_type" => "daily_revenue_cap",
                "field_value" => 0,
            ];
            $field[] = [
                "field_type" => "daily_conversion_cap",
                "field_value" => 0,
            ];

            $field[] = [
                "field_type" => "weekly_click_cap",
                "field_value" => 0,
            ];
            $field[] = [
                "field_type" => "weekly_conversion_cap",
                "field_value" => 0,
            ];
            $field[] = [
                "field_type" => "weekly_payout_cap",
                "field_value" => 0,
            ];
            $field[] = [
                "field_type" => "weekly_revenue_cap",
                "field_value" => 0,
            ];
            $field[] = [
                "field_type" => "weekly_conversion_cap",
                "field_value" => 0,
            ];

            $field[] = [
                "field_type" => "monthly_click_cap",
                "field_value" => 0,
            ];
            $field[] = [
                "field_type" => "monthly_conversion_cap",
                "field_value" => 0,
            ];
            $field[] = [
                "field_type" => "monthly_payout_cap",
                "field_value" => 0,
            ];
            $field[] = [
                "field_type" => "monthly_revenue_cap",
                "field_value" => 0,
            ];
            $field[] = [
                "field_type" => "monthly_conversion_cap",
                "field_value" => 0,
            ];

            $field[] = [
                "field_type" => "global_click_cap",
                "field_value" => 0,
            ];
            $field[] = [
                "field_type" => "global_conversion_cap",
                "field_value" => 0,
            ];
            $field[] = [
                "field_type" => "global_payout_cap",
                "field_value" => 0,
            ];
            $field[] = [
                "field_type" => "global_revenue_cap",
                "field_value" => 0,
            ];
            $field[] = [
                "field_type" => "global_conversion_cap",
                "field_value" => 0,
            ];
        }

        $param['fields'] = $field;

        $result = $this->curlPatch($url, $param);

        if (isset($result->result)) {
            return ['updated' => $result->result];
        } else {
            $message = isset($result->Error) ? $result->Error : $result->error;
            return ['updated' => 0, 'message' => $message];
        }
    }


    public function updateOfferStatus(modelRequestStatus $dataStatus)
    {
        $dataOffer = $dataStatus->offer;

        if(!$dataOffer->ef_id){
            throw new Exception('Empty offer EverFlow id.');
        }

        $url = "/networks/offerstyped";

        $param['network_offer_ids'] = [$dataOffer->ef_id];

        $field[] = [
            "field_type" => "offer_status",
            "field_value" => $dataStatus->ef_status,
        ];

        $param['fields'] = $field;

        $result = $this->curlPatch($url, $param);

        if (isset($result->result)) {
            return ['updated' => $result->result];
        } else {
            $message = isset($result->Error) ? $result->Error : $result->error;
            return ['updated' => 0, 'message' => $message];
        }
    }


    public function updateOfferPrice(modelRequestPrice $dataPrice)
    {
        $dataOffer = $dataPrice->offer;

        $campaignType = $dataOffer->campaign_type_param->ef_key ? : 'blank';
        $revenueType = [
            'blank' => 'blank',
            'cpc' => 'rpc',
            'cpa' => 'rpa',
            'cpm' => 'rpm',
            'cps' => 'rps',
            'cpa_cps' => 'rpa_rps',
        ];

        $dataExist = $this->searchPayoutRevenue($dataPrice, $dataOffer);

        if($dataExist && (!isset($dataExist->date_valid_from) || !$dataExist->date_valid_from) && !$dataPrice->date){

            $url = "/networks/custom/payoutrevenue/$dataExist->network_custom_payout_revenue_setting_id";

            $param = [
                "custom_setting_status" => "active",
                "network_custom_payout_revenue_setting_id" => $dataExist->network_custom_payout_revenue_setting_id,
                "network_offer_id" => $dataOffer->ef_id,
                'name' => 'Base',
                'payout_type' => $campaignType, /*blank, cpc, cpa, cpm, cps, cpa_cps, prv, null_value*/
                //$param'payout_percentage', /*if payout_type = cpa_cps*/
                "payout_amount" => floatval($dataPrice->price_out),
                "revenue_type" => isset($revenueType[$campaignType]) ? $revenueType[$campaignType] : "blank", /*blank, rpc, rpa, rpm, rps, rpa_rps*/
                //'revenue_percentage', /*if revenue_type = rpa_rps*/
                "revenue_amount" => floatval($dataPrice->price_in),
                "is_custom_payout_enabled" => true,
                "is_custom_revenue_enabled" => true,
                "network_offer_payout_revenue_id" => 0,
            ];

            if($dataPrice->affiliate_all){
                $param['is_apply_all_affiliates'] = true;
            } else {

                $param['is_apply_all_affiliates'] = false;
                $param['network_affiliate_ids'] = [$dataPrice->affiliate->ef_id];
            }

            $result = $this->curlPut($url, $param);

            if($result->network_custom_payout_revenue_setting_id){
                return ['ef_id' => $result->network_custom_payout_revenue_setting_id];
            } else {
                $message = isset($result->Error) ? $result->Error : $result->error;
                return ['ef_id' => 0, 'message' => $message];
            }

        } else {

            $url = "/networks/custom/payoutrevenue";

            $param = [
                "custom_setting_status" => "active",
                "network_offer_id" => $dataOffer->ef_id,
                'name' => 'Base',
                'payout_type' => $campaignType, /*blank, cpc, cpa, cpm, cps, cpa_cps, prv, null_value*/
                //'payout_percentage', /*if payout_type = cpa_cps*/
                "payout_amount" => floatval($dataPrice->price_out),
                "revenue_type" => isset($revenueType[$campaignType]) ? $revenueType[$campaignType] : "blank", /*blank, rpc, rpa, rpm, rps, rpa_rps*/
                //'revenue_percentage', /*if revenue_type = rpa_rps*/
                "revenue_amount" => floatval($dataPrice->price_in),
                "is_custom_payout_enabled" => true,
                "is_custom_revenue_enabled" => true,
                "network_offer_payout_revenue_id" => 0,
            ];

            if($dataPrice->affiliate_all){
                $param['is_apply_all_affiliates'] = true;
            } else {
                $param['is_apply_all_affiliates'] = false;
                $param['network_affiliate_ids'] = [$dataPrice->affiliate->ef_id];
            }

            if($dataPrice->date){
                $param['date_valid_from'] = $dataPrice->date;
            }

            $result = $this->curlPost($url, $param);

            if(isset($result->network_custom_payout_revenue_setting_id)){
                return ['ef_id' => $result->network_custom_payout_revenue_setting_id];
            } else {
                $message = isset($result->Error) ? $result->Error : $result->error;
                return ['ef_id' => 0, 'message' => $message];
            }
        }
    }


    public function getPayoutRevenue($offer_id)
    {
        $url = "/networks/custom/payoutrevenue?filter=network_offer_id=$offer_id";

        $result = $this->curlGet($url);

        if(isset($result->custom_payout_revenue_settings)){
            return $result->custom_payout_revenue_settings;
        } else {
            return false;
        }
    }


    public function searchPayoutRevenue(modelRequestPrice $dataPrice, modelOffer $dataOffer)
    {
        $dataSearch = $this->getPayoutRevenue($dataOffer->ef_id);
        if($dataSearch){

            $affiliate_ef_id = $dataPrice->affiliate->ef_id;

            foreach($dataSearch as $iter){

                if($dataPrice->affiliate_all){
                    if($iter->is_apply_all_affiliates){
                        return $iter;
                    }
                } else {
                    foreach($iter->network_affiliate_ids as $affiliate_id){
                        if($affiliate_id == $affiliate_ef_id && count($iter->network_affiliate_ids) == 1){
                            return $iter;
                        }
                    }
                }
            }
        }

        return false;
    }


    public function updateOffer(modelOffer $data, modelRequestCap $cap)
    {
        if (!$data->ef_id) {
            throw new Exception('Empty offer EverFlow id.');
        }

        $dataEf = $this->getOffer($data->ef_id);

        $url = "/networks/offers/$data->ef_id";

        $param = [];

        if ($data->cap_type_id) {

            $capType = $data->cap_type;
            $capUnit = $data->cap_unit;

            $param['name'] = $dataEf->name;
            $param['currency_id'] = "USD";
            $param['destination_url'] = $dataEf->destination_url;
            $param['network_advertiser_id'] = $dataEf->network_advertiser_id;
            $param['offer_status'] = $dataEf->offer_status;
            $param['conversion_method'] = $dataEf->conversion_method;
            $param['network_category_id'] = $dataEf->network_category_id;
            $param['redirect_mode'] = 'standard';
            $param['visibility'] = 'require_approval';
            $param['session_definition'] = 'cookie';
            $param['network_tracking_domain_id'] = $dataEf->network_tracking_domain_id;
            $param['is_caps_enabled'] = true;

            switch ($capType->key) {
                case "perday" :

                    $param["daily_click_cap"] = 0;
                    $param["daily_conversion_cap"] = 0;
                    $param["daily_payout_cap"] = 0;
                    $param["daily_revenue_cap"] = 0;

                    if ($capUnit->key == "lead") {
                        $param["daily_conversion_cap"] = (int) $cap->cap;
                    }

                    break;
                case "perweek" :

                    $param["weekly_click_cap"] = 0;
                    $param["weekly_conversion_cap"] = 0;
                    $param["weekly_payout_cap"] = 0;
                    $param["weekly_revenue_cap"] = 0;

                    if ($capUnit->key == "lead") {
                        $param["weekly_conversion_cap"] = (int) $cap->cap;
                    }

                    break;
                case "permonth" :

                    $param["monthly_click_cap"] = 0;
                    $param["monthly_conversion_cap"] = 0;
                    $param["monthly_payout_cap"] = 0;
                    $param["monthly_revenue_cap"] = 0;

                    if ($capUnit->key == "lead") {
                        $param["monthly_conversion_cap"] = (int) $cap->cap;
                    }

                    break;
                case "total" :

                    $param["global_click_cap"] = 0;
                    $param["global_conversion_cap"] = 0;
                    $param["global_payout_cap"] = 0;
                    $param["global_revenue_cap"] = 0;

                    if ($capUnit->key == "lead") {
                        $param["global_conversion_cap"] = (int) $cap->cap;
                    }

                    break;
            }
        }

        if($cap->cap_reset) {

            $param['is_caps_enabled'] = false;

            $param["daily_click_cap"] = 0;
            $param["daily_conversion_cap"] = 0;
            $param["daily_payout_cap"] = 0;
            $param["daily_revenue_cap"] = 0;

            $param["global_click_cap"] = 0;
            $param["global_conversion_cap"] = 0;
            $param["global_payout_cap"] = 0;
            $param["global_revenue_cap"] = 0;

            $param["weekly_click_cap"] = 0;
            $param["weekly_conversion_cap"] = 0;
            $param["weekly_payout_cap"] = 0;
            $param["weekly_revenue_cap"] = 0;

            $param["monthly_click_cap"] = 0;
            $param["monthly_conversion_cap"] = 0;
            $param["monthly_payout_cap"] = 0;
            $param["monthly_revenue_cap"] = 0;
        }

        $relationship = [];

        if(isset($dataEf->relationship->category->name)) {
            $relationship['category']['name'] = $dataEf->relationship->category->name;
        }

        if(isset($dataEf->relationship->payout_revenue)){

            $rel_entries = [];

            foreach($dataEf->relationship->payout_revenue->entries as $key => $entries){

                $rel_entries[$key]['is_default'] = $entries->is_default;
                $rel_entries[$key]['is_private'] = $entries->is_private;
                $rel_entries[$key]['payout_amount'] = $entries->payout_amount;
                $rel_entries[$key]['payout_percentage'] = $entries->payout_percentage;
                $rel_entries[$key]['revenue_type'] = $entries->revenue_type;
                $rel_entries[$key]['revenue_amount'] = $entries->revenue_amount;
                $rel_entries[$key]['revenue_percentage'] = $entries->revenue_percentage;
                $rel_entries[$key]['revenue_type'] = $entries->revenue_type;
            }

            $relationship['payout_revenue']['entries'] = $rel_entries;
            $relationship['payout_revenue']['total'] = $dataEf->relationship->payout_revenue->total;
        }

        if(isset($dataEf->relationship->encoded_value)){
            $relationship['encoded_value'] = $dataEf->relationship->encoded_value;
        }

        if(isset($dataEf->relationship->is_locked_currency)){
            $relationship['is_locked_currency'] = $dataEf->relationship->is_locked_currency;
        }

        if($relationship) {
            $param['relationship'] = $relationship;
        }

        $result = $this->curlPut($url, $param);

        if (isset($result->network_offer_id)) {
            return ['ef_id' => $result->network_offer_id];
        } else {
            $message = isset($result->error) ? $result->error : $result->Error;
            return ['ef_id' => 0, 'message' => $message];
        }
    }


    public function getTracking($offer_id, $domain_id = 0, $url_id = 0)
    {
        $url = "/networks/offers/$offer_id/trackingdomain/$domain_id/urls/$url_id";

        $result = $this->curlGet($url);

        if (isset($result->tracking_urls)) {
            return $result;
        } else {
            return false;
        }
    }


    public function getTrackingAffiliate($offer_id, $affiliate_id, $url_id = 0, $domain_id = 0)
    {
        $url = "/networks/offers/$offer_id/trackingdomain/$domain_id/url/$affiliate_id/$url_id";

        $result = $this->curlGet($url);

        if (isset($result->url)) {
            return $result;
        } else {
            return false;
        }
    }


}





//public function getTestValue()
//{
//    $json = '{
//            "destination_url": "http:\/\/tracking.taptica.com\/aff_c?offer_id=150011&tt_appid=com.zynga.FarmVille2CountryEscape&aff_id=357886&tt_aff_clickid",
//    "labels": [],
//    "offer_status": "pending",
//    "network_tracking_domain_id": 130,
//    "is_use_secure_link": false,
//    "redirect_mode": "standard",
//    "conversion_method": "javascript",
//    "visibility": "require_approval",
//    "ruleset": {
//            "languages": [],
//        "device_makes": [],
//        "browsers": [],
//        "device_types": [],
//        "platforms": [],
//        "os_versions": [],
//        "countries": [
//            {
//                "targeting_type": "include",
//                "match_type": "exact",
//                "country_id": 227,
//            },
//            {
//                "targeting_type": "include",
//                "match_type": "exact",
//                "country_id": 2,
//            }
//        ],
//        "regions": [],
//        "cities": [],
//        "dmas": [],
//        "mobile_carriers": [],
//        "ips": [],
//        "connection_types": [],
//        "is_use_day_parting": false,
//        "day_parting_apply_to": "null_value",
//        "day_parting_timezone_id": 0,
//        "is_block_proxy": false
//    },
//    "is_fail_traffic_enabled": false,
//    "creatives": [],
//    "is_allow_deep_link": false,
//    "is_using_explicit_terms_and_conditions": false,
//    "is_using_suppression_list": false,
//    "is_view_through_enabled": false,
//    "is_duplicate_filter_enabled": false,
//    "is_must_approve_conversion": false,
//    "is_seo_friendly": false,
//    "is_allow_duplicate_conversion": false,
//    "is_session_tracking_enabled": false,
//    "is_use_scrub_rate": false,
//    "name": "Farmville Android US SAT",
//    "network_advertiser_id": 150,
//    "currency_id": "USD",
//    "network_category_id": 1,
//    "html_description": "Pixel fires on the install All Mobile, No Incent or Adult Farmville Android US SAT Farmville 2 Country Escape Android. Pixel will fire on the install.",
//    "payout_revenue": [
//        {
//            "is_default": true,
//            "is_private": false,
//            "entry_name": "Base",
//            "revenue_type": "rpc",
//            "revenue_amount": 1.8,
//            "payout_type": "cpc",
//            "payout_amount": 0.9,
//        }
//    ],
//    "session_definition": "cookie",
//    "session_duration": 720,
//    "duplicate_filter_targeting_action": "fail_traffic",
//    "is_caps_enabled": true,
//    "daily_click_cap": 500,
//    "weekly_click_cap": 0,
//    "monthly_click_cap": 0,
//    "global_click_cap": 0,
//    "daily_conversion_cap": 0,
//    "weekly_conversion_cap": 0,
//    "monthly_conversion_cap": 0,
//    "global_conversion_cap": 0,
//    "daily_payout_cap": 0,
//    "weekly_payout_cap": 0,
//    "monthly_payout_cap": 0,
//    "global_payout_cap": 0,
//    "daily_revenue_cap": 0,
//    "weekly_revenue_cap": 0,
//    "monthly_revenue_cap": 0,
//    "global_revenue_cap": 0,
//    "redirect_routing_method": "internal",
//    "redirect_internal_routing_type": "priority"
//}';
//
//    return $json;
//}
//
//
//$example = '{
//"app_identifier":"string (optional)",
//"conversion_method":"string (required)",
//"creatives":[{
//    "creative_status":"string (required)",
//    "creative_type":"string (required)",
//    "email_from":"string (required)",
//    "email_subject":"string (required)",
//    "height":"integer (required)",
//    "html_assets":[{
//        "content_type":"string (required)",
//        "file_size":"integer (required)",
//        "filename":"string (required)",
//        "image_height":"integer (required)",
//        "image_width":"integer (required)",
//        "url":"string (required)"
//    }],
//    "html_code":"string (required)",
//    "html_files":[{
//        "original_file_name":"string (required)",
//        "temp_url":"string (required)"
//    }],
//    "is_private":"boolean (required)",
//    "name":"string (required)",
//    "network_offer_id":"integer (required)",
//    "resource_file":{
//        "original_file_name":"string (required)",
//        "temp_url":"string (required)"
//    },
//    "width":"integer (required)"
//}],
//"currency_id":"string (required)",
//"daily_click_cap":"integer (optional)",
//"daily_conversion_cap":"integer (optional)",
//"daily_payout_cap":"number (optional)",
//"daily_revenue_cap":"number (optional)",
//"date_live_until":"string (optional)",
//"destination_url":"string (required)",
//"duplicate_filter_targeting_action":"string (required)",
//"global_click_cap":"integer (optional)",
//"global_conversion_cap":"integer (optional)",
//"global_payout_cap":"number (optional)",
//"global_revenue_cap":"number (optional)",
//"html_description":"string (optional)",
//"integrations":{
//    "ezepo":{"enabled":"boolean (required)"},
//    "forensiq":{
//        "action":"string (required)",
//        "click_threshold":"integer (required)",
//        "conversion_status":"string (required)",
//        "conversion_threshold":"integer (required)",
//        "network_offer_id":"integer (required)"
//    },
//    "offer_demand_partner":{
//        "is_enabled_remote_update":"boolean (required)",
//        "is_force_update":"boolean (required)",
//        "is_locked_status_update":"boolean (required)",
//        "is_remote_offer_active":"boolean (required)",
//        "last_value_md5":"string (required)",
//        "network_integration_demand_partner_id":"integer (required)",
//        "relationship":{
//            "logo_url":"string (required)",
//            "partner_name":"string (required)"
//        },
//        "remote_offer_id":"integer (required)"
//    },
//    "optizmo":{
//        "optoutlist_id":"string (required)",
//        "optoutlist_name":"string (required)"
//    },
//    "twentyfour_metrics":{
//        "network_integration_twentyfour_metrics_tracker_id":"integer (required)",
//        "tracker_name":"string (required)"
//    }
//},
//"internal_notes":"string (optional)",
//"internal_redirects":[{
//    "redirect_network_offer_group_id":"integer (required)",
//    "redirect_network_offer_id":"integer (required)",
//    "routing_value":"integer (required)",
//    "ruleset":{
//        "browsers":[{
//            "browser_id":"integer (required)",
//            "match_type":"string (required)",
//            "targeting_type":"string (required)"
//        }],
//        "cities":[{
//            "city_id":"integer (required)",
//            "match_type":"string (required)",
//            "targeting_type":"string (required)"
//        }],
//        "connection_types":[{
//            "connection_type_id":"integer (required)",
//            "match_type":"string (required)",
//            "targeting_type":"string (required)"
//        }],
//        "countries":[{
//            "country_id":"integer (required)",
//            "match_type":"string (required)",
//            "targeting_type":"string (required)"
//        }],
//        "day_parting_apply_to":"string (required)",
//        "day_parting_timezone_id":"integer (required)",
//        "days_parting":[{
//            "day_of_week":"integer (required)",
//            "end_hour":"integer (required)",
//            "end_minute":"integer (required)",
//            "start_hour":"integer (required)",
//            "start_minute":"integer (required)"
//        }],
//        "device_types":[{
//            "device_type_id":"integer (required)",
//            "match_type":"string (required)","targeting_type":"string (required)"
//        }],
//        "dmas":[{
//            "dma_code":"integer (required)",
//            "match_type":"string (required)",
//            "targeting_type":"string (required)"
//        }],
//        "ips":[{
//            "ip_from":"string (required)",
//            "ip_to":"string (required)",
//            "match_type":"string (required)",
//            "targeting_type":"string (required)"
//        }],
//        "is_block_proxy":"boolean (required)",
//        "is_use_day_parting":"boolean (required)",
//        "languages":[{
//            "browser_language_id":"integer (required)",
//            "match_type":"string (required)",
//            "targeting_type":"string (required)"
//        }],
//        "mobile_carriers":[{
//            "match_type":"string (required)",
//            "mobile_carrier_id":"integer (required)",
//            "targeting_type":"string (required)"
//        }],
//        "os_versions":[{
//            "match_type":"string (required)",
//            "os_version_id":"integer (required)",
//            "platform_id":"integer (required)",
//            "targeting_type":"string (required)"
//        }],
//        "platforms":[{
//            "match_type":"string (required)",
//            "platform_id":"integer (required)",
//            "targeting_type":"string (required)"
//        }],
//        "regions":[{
//            "match_type":"string (required)",
//            "region_id":"integer (required)",
//            "targeting_type":"string (required)"
//        }]
//    }
//}],
//"is_allow_deep_link":"boolean (required)",
//"is_allow_duplicate_conversion":"boolean (required)",
//"is_caps_enabled":"boolean (required)",
//"is_duplicate_filter_enabled":"boolean (required)",
//"is_fail_traffic_enabled":"boolean (required)",
//"is_must_approve_conversion":"boolean (required)",
//"is_seo_friendly":"boolean (required)",
//"is_session_tracking_enabled":"boolean (required)",
//"is_use_scrub_rate":"boolean (optional)",
//"is_use_secure_link":"boolean (required)",
//"is_using_explicit_terms_and_conditions":"boolean (required)",
//"is_using_suppression_list":"boolean (required)",
//"is_view_through_enabled":"boolean (required)",
//"is_view_through_session_tracking_enabled":"boolean (optional)",
//"is_whitelist_check_enabled":"boolean (optional)",
//"labels":["string"],
//"monthly_click_cap":"integer (optional)",
//"monthly_conversion_cap":"integer (optional)",
//"monthly_payout_cap":"number (optional)",
//"monthly_revenue_cap":"number (optional)",
//"name":"string (required)",
//"network_advertiser_id":"integer (required)",
//"network_category_id":"integer (required)",
//"network_offer_group_id":"integer (optional)",
//"network_tracking_domain_id":"integer (required)",
//"offer_status":"string (required)",
//"payout_revenue":[{
//    "entry_name":"string (required)",
//    "is_default":"boolean (required)",
//    "is_private":"boolean (required)",
//    "payout_amount":"number (required)",
//    "payout_percentage":"number (required)",
//    "payout_type":"string (required)",
//    "revenue_amount":"number (required)",
//    "revenue_percentage":"number (required)",
//    "revenue_type":"string (required)"
//}],
//"preview_url":"string (optional)",
//"project_id":"string (optional)",
//"redirect_internal_routing_type":"string (required)",
//"redirect_mode":"string (required)",
//"redirect_routing_method":"string (required)",
//"relationship":{
//    "advertiser":{
//        "account_status":"string (required)",
//        "address_id":"integer (optional)",
//        "billing":{
//            "tax_id":"string (required)"
//        },
//        "contact_address":{
//            "address_1":"string (required)",
//            "address_2":"string (required)",
//            "city":"string (required)",
//            "country_code":"string (required)",
//            "country_id":"integer (required)",
//            "region_code":"string (required)",
//            "zip_postal_code":"string (required)"
//        },
//        "default_currency_id":"string (required)",
//        "internal_notes":"string (optional)",
//        "is_contact_address_enabled":"boolean (optional)",
//        "is_expose_publisher_reporting_data":"boolean (optional)",
//        "labels":["string"],
//        "name":"string (required)",
//        "network_employee_id":"integer (required)",
//        "sales_manager_id":"integer (optional)",
//        "users":[{
//            "account_status":"string (required)",
//            "cell_phone":"string (required)",
//            "currency_id":"string (required)",
//            "email":"string (required)",
//            "first_name":"string (required)",
//            "initial_password":"string (required)",
//            "instant_messaging_id":"integer (required)",
//            "instant_messaging_identifier":"string (required)",
//            "language_id":"integer (required)",
//            "last_name":"string (required)",
//            "network_advertiser_id":"integer (required)",
//            "timezone_id":"integer (required)",
//            "title":"string (required)",
//            "work_phone":"string (required)"
//        }],
//        "verification_token":"string (optional)"
//    },
//    "audits":{
//        "entries":[{
//            "employee_name":"string (required)",
//            "operation_type":"string (required)",
//            "user_agent":"string (required)",
//            "user_ip":"string (required)"
//        }],
//        "total":"integer (required)"
//    },
//    "category":{"name":"string (required)"},
//    "creatives":{
//        "entries":[{
//            "creative_status":"string (required)",
//            "creative_type":"string (required)",
//            "email_from":"string (required)",
//            "email_subject":"string (required)",
//            "height":"integer (required)",
//            "html_assets":[{
//                "content_type":"string (required)",
//                "file_size":"integer (required)",
//                "filename":"string (required)",
//                "image_height":"integer (required)",
//                "image_width":"integer (required)",
//                "url":"string (required)"
//            }],
//            "html_code":"string (required)",
//            "html_files":[{
//                "original_file_name":"string (required)",
//                "temp_url":"string (required)"
//            }],
//            "is_private":"boolean (required)",
//            "name":"string (required)",
//            "network_offer_id":"integer (required)",
//            "resource_file":{
//                "original_file_name":"string (required)",
//                "temp_url":"string (required)"
//            },
//            "width":"integer (required)"
//        }],
//        "total":"integer (required)"
//    },
//    "custom_cap_settings":{
//        "entries":[{
//            "daily_click_cap":"integer (required)",
//            "daily_conversion_cap":"integer (required)",
//            "daily_payout_cap":"number (required)",
//            "daily_revenue_cap":"number (required)",
//            "global_click_cap":"integer (required)",
//            "global_conversion_cap":"integer (required)",
//            "global_payout_cap":"number (required)",
//            "global_revenue_cap":"number (required)",
//            "monthly_click_cap":"integer (required)",
//            "monthly_conversion_cap":"integer (required)",
//            "monthly_payout_cap":
//            "number (required)",
//            "monthly_revenue_cap":"number (required)",
//            "name":"string (required)",
//            "network_affiliate_id":"integer (required)",
//            "network_custom_cap_setting_id":"integer (required)",
//            "network_offer_id":"integer (required)",
//            "weekly_click_cap":"integer (required)",
//            "weekly_conversion_cap":"integer (required)",
//            "weekly_payout_cap":"number (required)",
//            "weekly_revenue_cap":"number (required)"
//        }],
//        "total":"integer (required)"
//    },
//    "custom_payout_revenue_settings":{
//        "entries":[{
//            "custom_setting_status":"string (required)",
//            "date_valid_from":"string (optional)",
//            "date_valid_to":"string (optional)",
//            "description":"string (optional)",
//            "is_apply_all_affiliates":"boolean (required)",
//            "is_custom_payout_enabled":"boolean (optional)",
//            "is_custom_revenue_enabled":"boolean (optional)",
//            "name":"string (required)",
//            "network_affiliate_ids":["integer"],
//            "network_custom_payout_revenue_setting_id":"integer (required)",
//            "network_offer_id":"integer (required)",
//            "network_offer_payout_revenue_id":"integer (required)",
//            "payout_amount":"number (required)",
//            "payout_percentage":"number (required)",
//            "payout_type":"string (required)",
//            "revenue_amount":"number (required)",
//            "revenue_percentage":"number (required)",
//            "revenue_type":"string (required)",
//            "ruleset":{
//                "browsers":[{
//                    "browser_id":"integer (required)",
//                    "match_type":"string (required)",
//                    "targeting_type":"string (required)"
//                }],
//                "cities":[{
//                    "city_id":"integer (required)",
//                    "match_type":"string (required)",
//                    "targeting_type":"string (required)"
//                }],
//                "connection_types":[{
//                    "connection_type_id":"integer (required)",
//                    "match_type":"string (required)",
//                    "targeting_type":"string (required)"
//                }],
//                "countries":[{
//                    "country_id":"integer (required)",
//                    "match_type":"string (required)",
//                    "targeting_type":"string (required)"
//                }],
//                "day_parting_apply_to":"string (required)",
//                "day_parting_timezone_id":"integer (required)",
//                "days_parting":[{
//                    "day_of_week":"integer (required)",
//                    "end_hour":"integer (required)",
//                    "end_minute":"integer (required)",
//                    "start_hour":"integer (required)",
//                    "start_minute":"integer (required)"
//                }],
//                "device_types":[{
//                    "device_type_id":"integer (required)",
//                    "match_type":"string (required)",
//                    "targeting_type":"string (required)"
//                }],
//                "dmas":[{
//                    "dma_code":"integer (required)",
//                    "match_type":"string (required)",
//                    "targeting_type":"string (required)"
//                }],
//                "ips":[{
//                    "ip_from":"string (required)",
//                    "ip_to":"string (required)",
//                    "match_type":"string (required)",
//                    "targeting_type":"string (required)"
//                }],
//                "is_block_proxy":"boolean (required)",
//                "is_use_day_parting":"boolean (required)",
//                "languages":[{
//                    "browser_language_id":"integer (required)",
//                    "match_type":"string (required)",
//                    "targeting_type":"string (required)"
//                }],
//                "mobile_carriers":[{
//                    "match_type":"string (required)",
//                    "mobile_carrier_id":"integer (required)",
//                    "targeting_type":"string (required)"
//                }],
//                "os_versions":[{
//                    "match_type":"string (required)",
//                    "os_version_id":"integer (required)",
//                    "platform_id":"integer (required)",
//                    "targeting_type":"string (required)"
//                }],
//                "platforms":[{
//                    "match_type":"string (required)",
//                    "platform_id":"integer (required)",
//                    "targeting_type":"string (required)"
//                }],
//                "regions":[{
//                    "match_type":"string (required)",
//                    "region_id":"integer (required)",
//                    "targeting_type":"string (required)"
//                }]
//            },
//            "variables":[{
//                "comparison_method":"string (required)",
//                "variable":"string (required)",
//                "variable_value":"string (required)"
//            }]
//        }],
//        "total":"integer (required)"
//    },
//    "custom_scrub_rate_settings":{
//        "entries":[{
//            "custom_setting_status":"string (required)",
//            "name":"string (required)",
//            "network_affiliate_id":"integer (required)",
//            "network_custom_scrub_rate_setting_id":"integer (required)",
//            "network_offer_id":"integer (required)",
//            "scrub_rate_percentage":"integer (required)",
//            "scrub_rate_status":"string (required)",
//            "variables":[{
//                "comparison_method":"string (required)",
//                "variable":"string (required)",
//                "variable_value":"string (required)"
//            }]
//        }],
//        "total":"integer (required)"
//    },
//    "encoded_value":"string (required)",
//    "integrations":{
//        "ezepo":{"enabled":"boolean (required)"},
//        "forensiq":{
//            "action":"string (required)",
//            "click_threshold":"integer (required)",
//            "conversion_status":"string (required)",
//            "conversion_threshold":"integer (required)",
//            "network_offer_id":"integer (required)"
//        },
//        "offer_demand_partner":{
//            "is_enabled_remote_update":"boolean (required)",
//            "is_force_update":"boolean (required)",
//            "is_locked_status_update":"boolean (required)",
//            "is_remote_offer_active":"boolean (required)",
//            "last_value_md5":"string (required)",
//            "network_integration_demand_partner_id":"integer (required)",
//            "relationship":{
//                "logo_url":"string (required)",
//                "partner_name":"string (required)"},
//                "remote_offer_id":"integer (required)"},
//                "optizmo":{
//                    "optoutlist_id":"string (required)",
//                    "optoutlist_name":"string (required)"
//                },
//                "twentyfour_metrics":{
//                    "network_integration_twentyfour_metrics_tracker_id":"integer (required)",
//                    "tracker_name":"string (required)"
//                }
//            },
//            "internal_redirects":{
//                "entries":[{
//                    "redirect_network_offer_group_id":"integer (required)",
//                    "redirect_network_offer_id":"integer (required)",
//                    "routing_value":"integer (required)",
//                    "ruleset":{
//                        "browsers":[{
//                            "browser_id":"integer (required)",
//                            "match_type":"string (required)",
//                            "targeting_type":"string (required)"
//                        }],
//                        "cities":[{
//                            "city_id":"integer (required)",
//                            "match_type":"string (required)",
//                            "targeting_type":"string (required)"
//                        }],
//                        "connection_types":[{
//                            "connection_type_id":"integer (required)",
//                            "match_type":"string (required)",
//                            "targeting_type":"string (required)"
//                        }],
//                        "countries":[{
//                            "country_id":"integer (required)",
//                            "match_type":"string (required)",
//                            "targeting_type":"string (required)"
//                        }],
//                        "day_parting_apply_to":"string (required)",
//                        "day_parting_timezone_id":"integer (required)",
//                        "days_parting":[{
//                            "day_of_week":"integer (required)",
//                            "end_hour":"integer (required)",
//                            "end_minute":"integer (required)",
//                            "start_hour":"integer (required)",
//                            "start_minute":"integer (required)"
//                        }],
//                        "device_types":[{
//                            "device_type_id":"integer (required)",
//                            "match_type":"string (required)",
//                            "targeting_type":"string (required)"
//                        }],
//                        "dmas":[{
//                            "dma_code":"integer (required)",
//                            "match_type":"string (required)",
//                            "targeting_type":"string (required)"
//                        }],
//                        "ips":[{
//                            "ip_from":"string (required)",
//                            "ip_to":"string (required)",
//                            "match_type":"string (required)",
//                            "targeting_type":"string (required)"
//                        }],
//                        "is_block_proxy":"boolean (required)",
//                        "is_use_day_parting":"boolean (required)",
//                        "languages":[{
//                            "browser_language_id":"integer (required)",
//                            "match_type":"string (required)",
//                            "targeting_type":"string (required)"
//                        }],
//                        "mobile_carriers":[{
//                            "match_type":"string (required)",
//                            "mobile_carrier_id":"integer (required)",
//                            "targeting_type":"string (required)"
//                        }],
//                        "os_versions":[{
//                            "match_type":"string (required)",
//                            "os_version_id":"integer (required)",
//                            "platform_id":"integer (required)",
//                            "targeting_type":"string (required)"
//                        }],
//                        "platforms":[{
//                            "match_type":"string (required)",
//                            "platform_id":"integer (required)",
//                            "targeting_type":"string (required)"
//                        }],
//                        "regions":[{
//                            "match_type":"string (required)",
//                            "region_id":"integer (required)",
//                            "targeting_type":"string (required)"
//                        }]
//                    }
//                }],
//                "total":"integer (required)"},
//                "is_locked_currency":"boolean (required)",
//                "labels":{
//                    "entries":["string"],
//                    "total":"integer (required)"
//                },
//                "offer_group":{
//                    "name":"string (required)",
//                    "network_advertiser_id":"integer (required)",
//                    "offer_count":"integer (required)",
//                    "offer_group_status":"string (required)"
//                },
//                "payout_revenue":{
//                    "entries":[{
//                        "entry_name":"string (required)",
//                        "is_default":"boolean (required)",
//                        "is_private":"boolean (required)",
//                        "payout_amount":"number (required)",
//                        "payout_percentage":"number (required)",
//                        "payout_type":"string (required)",
//                        "revenue_amount":"number (required)",
//                        "revenue_percentage":"number (required)",
//                        "revenue_type":"string (required)"
//                    }],
//                    "total":"integer (required)"
//                },
//                "remaining_caps":{},
//                "reporting":{
//                    "cpa":"number (required)",
//                    "cpc":"number (required)",
//                    "ctr":"number (required)",
//                    "cv":"integer (required)",
//                    "cvr":"number (required)",
//                    "duplicate_click":"integer (required)",
//                    "epc":"number (required)",
//                    "event":"integer (required)",
//                    "event_revenue":"number (required)",
//                    "evr":"number (required)",
//                    "gross_sales":"number (required)",
//                    "imp":"integer (required)",
//                    "invalid_click":"integer (required)",
//                    "invalid_cv_scrub":"integer (required)",
//                    "margin":"number (required)",
//                    "payout":"number (required)",
//                    "profit":"number (required)",
//                    "revenue":"number (required)",
//                    "roas":"number (required)",
//                    "rpa":"number (required)",
//                    "rpc":"number (required)",
//                    "total_click":"integer (required)",
//                    "total_cv":"integer (required)",
//                    "unique_click":"integer (required)",
//                    "view_through_cv":"integer (required)"
//                },
//                "ruleset":{
//                    "browsers":[{
//                        "browser_id":"integer (required)",
//                        "match_type":"string (required)",
//                        "targeting_type":"string (required)"
//                    }],
//                    "cities":[{
//                        "city_id":"integer (required)",
//                        "match_type":"string (required)",
//                        "targeting_type":"string (required)"
//                    }],
//                    "connection_types":[{
//                        "connection_type_id":"integer (required)",
//                        "match_type":"string (required)",
//                        "targeting_type":"string (required)"
//                    }],
//                    "countries":[{
//                        "country_id":"integer (required)",
//                        "match_type":"string (required)",
//                        "targeting_type":"string (required)"
//                    }],
//                    "day_parting_apply_to":"string (required)",
//                    "day_parting_timezone_id":"integer (required)",
//                    "days_parting":[{
//                        "day_of_week":"integer (required)",
//                        "end_hour":"integer (required)",
//                        "end_minute":"integer (required)",
//                        "start_hour":"integer (required)",
//                        "start_minute":"integer (required)"
//                    }],
//                    "device_types":[{
//                        "device_type_id":"integer (required)",
//                        "match_type":"string (required)",
//                        "targeting_type":"string (required)"
//                    }],
//                    "dmas":[{
//                        "dma_code":"integer (required)",
//                        "match_type":"string (required)",
//                        "targeting_type":"string (required)"
//                    }],
//                    "ips":[{
//                        "ip_from":"string (required)",
//                        "ip_to":"string (required)",
//                        "match_type":"string (required)",
//                        "targeting_type":"string (required)"
//                    }],
//                    "is_block_proxy":"boolean (required)",
//                    "is_use_day_parting":"boolean (required)",
//                    "languages":[{
//                        "browser_language_id":"integer (required)",
//                        "match_type":"string (required)",
//                        "targeting_type":"string (required)"
//                    }],
//                    "mobile_carriers":[{
//                        "match_type":"string (required)",
//                        "mobile_carrier_id":"integer (required)",
//                        "targeting_type":"string (required)"
//                    }],
//                    "os_versions":[{
//                        "match_type":"string (required)",
//                        "os_version_id":"integer (required)",
//                        "platform_id":"integer (required)",
//                        "targeting_type":"string (required)"
//                    }],
//                    "platforms":[{
//                        "match_type":"string (required)",
//                        "platform_id":"integer (required)",
//                        "targeting_type":"string (required)"
//                    }],
//                    "regions":[{
//                        "match_type":"string (required)",
//                        "region_id":"integer (required)",
//                        "targeting_type":"string (required)"
//                    }]
//                },
//                "source_names":{
//                    "entries":["string"],
//                    "total":"integer (required)"
//                },
//                "thumbnail_asset":{
//                    "content_type":"string (required)",
//                    "file_size":"integer (required)",
//                    "filename":"string (required)",
//                    "image_height":"integer (required)",
//                    "image_width":"integer (required)",
//                    "url":"string (required)"
//                },
//                "thumbnail_file":{
//                    "original_file_name":"string (required)",
//                    "temp_url":"string (required)"
//                },
//                "traffic_filters":{
//                    "entries":[{
//                        "action":"string (required)",
//                        "match_type":"string (required)",
//                        "parameter":"string (required)",
//                        "value":"string (required)"
//                    }],
//                    "total":"integer (required)"},
//                    "urls":{
//                        "entries":[{
//                            "destination_url":"string (required)",
//                            "name":"string (required)",
//                            "network_offer_id":"integer (required)",
//                            "preview_url":"string (required)",
//                            "url_status":"string (required)"
//                        }],
//                        "total":"integer (required)"
//                    },
//                    "visibility":{}
//                },
//                "ruleset":{
//                    "browsers":[{
//                        "browser_id":"integer (required)",
//                        "match_type":"string (required)",
//                        "targeting_type":"string (required)"
//                    }],
//                    "cities":[{
//                        "city_id":"integer (required)",
//                        "match_type":"string (required)",
//                        "targeting_type":"string (required)"
//                    }],
//                    "connection_types":[{
//                        "connection_type_id":"integer (required)",
//                        "match_type":"string (required)",
//                        "targeting_type":"string (required)"
//                    }],
//                    "countries":[{
//                        "country_id":"integer (required)",
//                        "match_type":"string (required)",
//                        "targeting_type":"string (required)"
//                    }],
//                    "day_parting_apply_to":"string (required)",
//                    "day_parting_timezone_id":"integer (required)",
//                    "days_parting":[{
//                        "day_of_week":"integer (required)",
//                        "end_hour":"integer (required)",
//                        "end_minute":"integer (required)",
//                        "start_hour":"integer (required)",
//                        "start_minute":"integer (required)"
//                    }],
//                    "device_types":[{
//                        "device_type_id":"integer (required)",
//                        "match_type":"string (required)",
//                        "targeting_type":"string (required)"
//                    }],
//                    "dmas":[{
//                        "dma_code":"integer (required)",
//                        "match_type":"string (required)",
//                        "targeting_type":"string (required)"
//                    }],
//                    "ips":[{
//                        "ip_from":"string (required)",
//                        "ip_to":"string (required)",
//                        "match_type":"string (required)",
//                        "targeting_type":"string (required)"
//                    }],
//                    "is_block_proxy":"boolean (required)",
//                    "is_use_day_parting":"boolean (required)",
//                    "languages":[{
//                        "browser_language_id":"integer (required)",
//                        "match_type":"string (required)",
//                        "targeting_type":"string (required)"
//                    }],
//                    "mobile_carriers":[{
//                        "match_type":"string (required)",
//                        "mobile_carrier_id":"integer (required)",
//                        "targeting_type":"string (required)"
//                    }],
//                    "os_versions":[{
//                        "match_type":"string (required)",
//                        "os_version_id":"integer (required)",
//                        "platform_id":"integer (required)",
//                        "targeting_type":"string (required)"
//                    }],
//                    "platforms":[{
//                        "match_type":"string (required)",
//                        "platform_id":"integer (required)",
//                        "targeting_type":"string (required)"
//                    }],
//                    "regions":[{
//                        "match_type":"string (required)",
//                        "region_id":"integer (required)",
//                        "targeting_type":"string (required)"
//                    }]
//                },
//                "scrub_rate_percentage":"integer (optional)",
//                "scrub_rate_status":"string (optional)",
//                "server_side_url":"string (optional)",
//                "session_definition":"string (required)",
//                "session_duration":"integer (required)",
//                "session_tracking_lifespan_hour":"integer (optional)",
//                "session_tracking_minimum_lifespan_second":"integer (optional)",
//                "session_tracking_start_on":"string (optional)",
//                "source_names":["string"],
//                "suppression_list_id":"integer (required)",
//                "terms_and_conditions":"string (optional)",
//                "thumbnail_file":{
//                    "original_file_name":"string (required)",
//                    "temp_url":"string (required)"
//                },
//                "thumbnail_url":"string (optional)",
//                "traffic_filters":[{
//                    "action":"string (required)","match_type":"string (required)",
//                    "parameter":"string (required)","value":"string (required)"
//                }],
//                "view_through_destination_url":"string (optional)",
//                "view_through_session_tracking_lifespan_minute":"integer (optional)",
//                "view_through_session_tracking_minimal_lifespan_second":"integer (optional)",
//                "visibility":"string (required)",
//                "weekly_click_cap":"integer (optional)",
//                "weekly_conversion_cap":"integer (optional)",
//                "weekly_payout_cap":"number (optional)",
//                "weekly_revenue_cap":"number (optional)"
//            }';
