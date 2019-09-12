<?php
namespace App\Services\EverFlow;

use App\Models\Massadjustment as modelMassadjustment;

class Adjustment extends Core
{

    public function __construct()
    {
        parent::__construct();
    }


    public function changeReport($data)
    {
        $model = new modelMassadjustment();

        $exist = $model->where('affiliate_id', $data['network_affiliate_id'])
            ->where('offer_id', $data['network_offer_id'])
            ->where('date', $data['date_adjustment'])
            ->where('type', 1)
            ->first();

        if($exist){
            if($exist->compareWith($data, 1)){
                $result = $this->apiUpdate($exist->ef_adjustment_id, $data);
                if($result){
                    $exist->fill([
                        'ef_adjustment_id' => $result->network_reporting_adjustment_id,
                        'affiliate_id' => $data['network_affiliate_id'],
                        'offer_id' => $data['network_offer_id'],
                        'date' => $data['date_adjustment'],
                        'total_click' => $data['total_clicks'],
                        'unique_click' => $data['unique_clicks'],
                        'conversion' => $data['conversions'],
                        'payout' => $data['payout'],
                        'revenue' => $data['revenue'],
                        'type' => 1,
                        'note' => $data['notes'],
                    ]);
                    $exist->save();
                }
            }
        } else {
            $result = $this->apiCreate($data);
            if($result){
                $new = new modelMassadjustment();
                $new->fill([
                    'ef_adjustment_id' => $result->network_reporting_adjustment_id,
                    'affiliate_id' => $data['network_affiliate_id'],
                    'offer_id' => $data['network_offer_id'],
                    'date' => $data['date_adjustment'],
                    'total_click' => $data['total_clicks'],
                    'unique_click' => $data['unique_clicks'],
                    'conversion' => $data['conversions'],
                    'payout' => $data['payout'],
                    'revenue' => $data['revenue'],
                    'type' => 1,
                    'note' => $data['notes'],
                    'relationship' => json_encode($result->relationship->snapshot),
                    'created_by' => 'cron',
                    'created_by_id' => 0,
                ]);
                $new->save();
            }
        }
    }


    protected function apiCreate($param)
    {
        $url = "/networks/reportingadjustments";

        $result = $this->curlPost($url, $param);

        if(isset($result->network_reporting_adjustment_id)){
            return $result;
        } else {
            return false;
        }
    }


    protected function apiUpdate($id, $param)
    {
        $url = "/networks/reportingadjustments/$id";

        $result = $this->curlPut($url, $param);

        if($result->network_reporting_adjustment_id){
            return $result;
        } else {
            return false;
        }
    }


}