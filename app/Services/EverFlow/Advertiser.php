<?php
namespace App\Services\EverFlow;

use App\Models\Advertiser as modelAdvertiser;

class Advertiser extends Core {

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllAdvertiser($page, $page_size)
    {
        $url = "/networks/advertisers?page=$page&page_size=$page_size";

        $result = $this->curlGet($url);

        if(isset($result->advertisers)){
            return $result;
        } else {
            return false;
        }
    }

    public function createAdvertiser(modelAdvertiser $dataAdvertiser)
    {
        $param_for_example = [
            "account_status" => "string (required)",
            "address_id" => "integer (optional)",
            "billing" => [
                "tax_id" => "string (required)"
            ],
            "contact_address" => [
                "address_1" => "string (required)",
                "address_2" => "string (required)",
                "city" => "string (required)",
                "country_code" => "string (required)",
                "country_id" => "integer (required)",
                "region_code" => "string (required)",
                "zip_postal_code" => "string (required)"
            ],
            "internal_notes" => "string (optional)",
            "is_contact_address_enabled" => "boolean (optional)",
            "is_expose_publisher_reporting_data" => "boolean (optional)",
            "labels" => "string",
            "name" => "string (required)",
            "network_employee_id" => "integer (required)",
            "sales_manager_id" => "integer (optional)",
            "users" => [
                "account_status" => "string (required)",
                "cell_phone" => "string (required)",
                "email" => "string (required)",
                "first_name" => "string (required)",
                "initial_password" => "string (required)",
                "instant_messaging_id" => "integer (required)",
                "instant_messaging_identifier" => "string (required)",
                "language_id" => "integer (required)",
                "last_name" => "string (required)",
                "network_advertiser_id" => "integer (required)",
                "timezone_id" => "integer (required)",
                "title" => "string (required)",
                "work_phone" => "string (required)"
            ]
        ];

        $url = "/networks/advertisers";

        $param = [
            "account_status" => $dataAdvertiser->ef_status, /*active, inactive, pending, suspended*/
            "default_currency_id" => $dataAdvertiser->currency->key,
            "contact_address" => [
                "address_1" => $dataAdvertiser->street1,
                "address_2" => $dataAdvertiser->street2,
                "city" => $dataAdvertiser->city,
                "country_code" => $dataAdvertiser->country,
                "country_id" => $dataAdvertiser->country_param->ef_id,
                "zip_postal_code" => $dataAdvertiser->zip
            ],
            "billing" => [
                "billing_frequency" => "other",
            ],
            "name" => $dataAdvertiser->name,
            "sales_manager_id" => $dataAdvertiser->manager->ef_id,
        ];

        if($dataAdvertiser->country == "US"){
            $param['region_code'] = $dataAdvertiser->state;
        }

        if($dataAdvertiser->manager_account_id){
            $param["network_employee_id"] = $dataAdvertiser->manager_account->ef_id;
        }

        $result = $this->curlPost($url, $param);

        if(isset($result->network_advertiser_id)){
            return $result->network_advertiser_id;
        } else {
            return false;
        }
    }

    public function updateAdvertiser(modelAdvertiser $dataAdvertiser)
    {
        $param_for_example = [
            "account_status" => "string (required)",
            "address_id" => "integer (optional)",
            "default_currency_id" => "string (required)",
            "billing" => [
                "tax_id" => "string (required)"
            ],
            "contact_address" => [
                "address_1" => "string (required)",
                "address_2" => "string (required)",
                "city" => "string (required)",
                "country_code" => "string (required)",
                "country_id" => "integer (required)",
                "region_code" => "string (required)",
                "zip_postal_code" => "string (required)"
            ],
            "internal_notes" => "string (optional)",
            "is_contact_address_enabled" => "boolean (optional)",
            "is_expose_publisher_reporting_data" => "boolean (optional)",
            "labels" => ["string"],
            "name" => "string (required)",
            "network_employee_id" => "integer (required)",
            "sales_manager_id" => "integer (optional)",
            "users" => [[
                "account_status" => "string (required)",
                "cell_phone" => "string (required)",
                "email" => "string (required)",
                "first_name" => "string (required)",
                "initial_password" => "string (required)",
                "instant_messaging_id" => "integer (required)",
                "instant_messaging_identifier" => "string (required)",
                "language_id" => "integer (required)",
                "last_name" => "string (required)",
                "network_advertiser_id" => "integer (required)",
                "timezone_id" => "integer (required)",
                "title" => "string (required)",
                "work_phone" => "string (required)"
            ]],
        ];

        $url = "/networks/advertisers/$dataAdvertiser->ef_id";

        $param = [
            "account_status" => $dataAdvertiser->ef_status, /*active, inactive, pending, suspended*/
            "default_currency_id" => $dataAdvertiser->currency->key,
            "contact_address" => [
                "address_1" => $dataAdvertiser->street1,
                "address_2" => $dataAdvertiser->street2,
                "city" => $dataAdvertiser->city,
                "country_code" => $dataAdvertiser->country,
                "country_id" => $dataAdvertiser->country_param->ef_id,
                "zip_postal_code" => $dataAdvertiser->zip
            ],
            "is_contact_address_enabled" => true,
            "name" => $dataAdvertiser->name,
            "sales_manager_id" => $dataAdvertiser->manager->ef_id,
        ];

        if($dataAdvertiser->country == "US"){
            $param['region_code'] = $dataAdvertiser->state;
        }

        if($dataAdvertiser->manager_account_id){
            $param["network_employee_id"] = $dataAdvertiser->manager_account->ef_id;
        }

        $result = $this->curlPut($url, $param);

        if(isset($result->network_advertiser_id)){
            return true;
        } else {
            return false;
        }

    }

    public function getAdvertiser($id)
    {
        $url = "/networks/advertisers/$id";

        $result = $this->curlGet($url);

        return $result;
    }


    public function getStat($dateStart, $dateEnd)
    {

        $param_for_example = [
            "columns" => [["column" => "string (required)"]],
            "currency_id" => "string (required)",
            "from" => "string (required)",
            "query" => [
                "filters" => [
                    "filter_id_value" => "string (required)",
                    "resource_type" => "string (required)"
                ],
                "settings" => ["campaign_data_only" => "boolean (required)"]
            ],
            "timezone_id" => "integer (required)",
            "to" => "string (required)"
        ];

        $dateStart = date("Y-m-d", strtotime($dateStart));
        $dateEnd = date("Y-m-d", strtotime($dateEnd));

        $url = "/networks/reporting/entity";

        $param = [
            "columns" => [["column" => "advertiser"], ["column" => "date"]],
            "currency_id" => "USD",
            "from" => $dateStart,
            "to" => $dateEnd,
            "timezone_id" => 80, /*America/New_York*/
            "query" => [
                "filters" => [],
            ],
        ];

        $result = $this->curlPost($url, $param);

        if(isset($result->table)){
            return $result;
        } else {
            var_dump($result);
            return false;
        }
    }

}