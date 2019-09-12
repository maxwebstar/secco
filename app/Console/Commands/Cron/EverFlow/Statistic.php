<?php

namespace App\Console\Commands\Cron\EverFlow;

use Illuminate\Console\Command;

use App\Services\EverFlow\General as EF_General;
use App\Services\EverFlow\Affiliate as EF_Affiliate;
use App\Services\EverFlow\Campaign as EF_Campaign;
use App\Services\EverFlow\Adjustment as EF_Adjustment;
use App\Services\LinkTrust\Affiliate as LT_Affiliate;

use App\Models\Massadjustment as modelMassadjustment;
use App\Models\Tmp\EF\Affiliate as model_tmp_ef_Affiliate;
use App\Models\Tmp\EF\Offer as model_tmp_ef_Offer;

use DateTime;
use DateTimeZone;
use Exception;

class Statistic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron/everflow:statistic {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronization of statistic';

    protected $result;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->result = [
            'affiliate' => [
                'count' => 0,
                'not_found' => [],
            ],
            'offer' => [
                'count' => 0,
                'not_found' => [],
            ],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $type = $this->argument('type');

        switch ($type) {
            case "import-from-lt-manual" :
                $this->importFromLTManual();
                break;
            case "import-from-lt-yesterday" :
                $this->importFromLTYesterday();
            case "import-from-ef-data" :
                $this->loadEFData();
            default :
                throw new Exception('Empty type commaand.');
                break;
        }
    }


    protected function importFromLTManual()
    {
//        $dateStart = new DateTime();
//        $dateStart->modify('first day of -1 month');
//
//        $dateEnd = new DateTime();
//        $dateEnd->modify('last day of -1 month');

        $dateStart = new DateTime();
        $dateStart->setDate(2018, 8, 1);
        $dateEnd = new DateTime();
        $dateEnd->setDate(2018, 8, 4);

        while($dateStart < $dateEnd){

            $dateCurrent = $dateStart->format('n/j/Y');

            $this->cycleImport($dateCurrent);

            $dateStart->modify('+1 day');
        }

        var_dump($this->result);
    }


    protected function importFromLTYesterday()
    {
        $dateStart = new DateTime();
        $dateStart->modify('-1 day');

        $dateEnd = new DateTime();
        $dateEnd->modify('-1 day');

        $dateCurrent = $dateStart->format('n/j/Y');

        $this->cycleImport($dateCurrent);

        var_dump($this->result);
    }


    protected function cycleImport($date)
    {
        $dateEU = date('Y-m-d', strtotime($date));

        $ef_Campaign = new EF_Campaign();
        $ef_Affiliate = new EF_Affiliate();
        $lt_Affiliate = new LT_Affiliate();
        $lt_Adjustment = new EF_Adjustment();

        $dataStat = $lt_Affiliate->getStat($date, $date);

        if($dataStat){
            foreach($dataStat->Affiliate as $iter_Affiliate){

                $affiliate_id = $this->getAffiliate($iter_Affiliate, $dateEU);
                if($affiliate_id){

//                    $attr_Affiliate = $iter_Affiliate->attributes();
//                    $name_affiliate = (string) $attr_Affiliate['Name'];
//                    if($name_affiliate != "AdHawk Media"){ /*ROCKETUNITY INC fka Garrett Chew*/
//                        continue;
//                    }
//                    var_dump($name_affiliate);
//                    var_dump($affiliate_id);

                    $this->result['affiliate']['count'] ++;

                    if(isset($iter_Affiliate->Campaigns->Campaign) && count($iter_Affiliate->Campaigns->Campaign)){

                        foreach($iter_Affiliate->Campaigns->Campaign as $iter_Campaign){

                            $offer_id = $this->getOffer($iter_Campaign, $dateEU);
                            if($offer_id){

                                $lt_Statistic = $iter_Campaign->StatRow->Statistics;

                                $total_click = $lt_Statistic->Clicks + $lt_Statistic->ClickGeoTargeted + $lt_Statistic->ClickDuplicate + $lt_Statistic->ClickExpired;

                                $dataAdjustment = [
                                    "network_affiliate_id" => intval($affiliate_id),
                                    "network_offer_id" => intval($offer_id),
                                    "total_clicks" => intval((string) $total_click),
                                    "unique_clicks" => intval((string) $lt_Statistic->Clicks),
                                    "conversions" => intval((string) $lt_Statistic->Approved),
                                    "payout" => floatval(str_replace("$", "", $lt_Statistic->Commission)),
                                    "revenue" => floatval(str_replace("$", "", $lt_Statistic->Revenue)),
                                    "date_adjustment" => $dateEU,
                                    "notes" => "This reporting adjustment was created via the API with data from linktrust",
                                ];

                                if($dataAdjustment['conversions'] || $dataAdjustment['payout'] || $dataAdjustment['revenue']){
                                    $lt_Adjustment->changeReport($dataAdjustment);
                                }

                                $this->result['offer']['count'] ++;
                            }
                        }
                    }
                }
            }
        }
    }


    protected function loadAffiliate()
    {
        $class = new EF_General();
        $class->loadAffiliate();
    }


    protected function loadOffer()
    {
        $class = new EF_General();
        $class->loadOffer();
    }


    protected function getAffiliate($iter_Affiliate, $date)
    {
        $model_tmp_ef_Affiliate = new model_tmp_ef_Affiliate();

        $attr_Affiliate = $iter_Affiliate->attributes();
        $lt_Affiliate_id = (string) $attr_Affiliate['Id'];
        $lt_Affiliate_name = (string) $attr_Affiliate['Name'];

        $dataAffiliate = $model_tmp_ef_Affiliate->where('network_affiliate_id', $lt_Affiliate_id)->first();
        if($dataAffiliate){

//            if($dataAffiliate->name == $lt_Affiliate_name){
//
                return $lt_Affiliate_id;
//
//            } else {
//
//                $dataSearch = $model_tmp_ef_Affiliate->where('name', $lt_Affiliate_name)->get();
//                $countSearch = count($dataSearch);
//
//                if($countSearch == 1){
//                    return $dataSearch[0]->network_affiliate_id;
//                } else if($countSearch > 1) {
//                    $this->result['affiliate']['dublicate'][$lt_Affiliate_id] = $lt_Affiliate_name;
//                } else {
//                    $this->result['affiliate']['not_found'][$lt_Affiliate_id] = $lt_Affiliate_name;
//                }
//            }

        } else {
            $this->result['affiliate']['not_found'][$lt_Affiliate_id] = $lt_Affiliate_name;
        }
    }


    protected function getOffer($iter_Campaign, $date)
    {
        $model_tmp_ef_Offer = new model_tmp_ef_Offer();

        $attr_Campaign = $iter_Campaign->attributes();
        $lt_Campaign_id = (string) $attr_Campaign['Id'];
        $lt_Campaign_name = (string) $attr_Campaign['Name'];

        $lt_Statistic = $iter_Campaign->StatRow->Statistics;

        $dataOffer = $model_tmp_ef_Offer->where('network_offer_id', $lt_Campaign_id)->first();
        if($dataOffer){

//            if($dataOffer->name == $lt_Campaign_name){
//
                return $lt_Campaign_id;
//
//            } else {
//
//                $dataSearch = $model_tmp_ef_Offer->where('name', $lt_Campaign_name)->get();
//                $countSearch = count($dataSearch);
//
//                if($countSearch == 1){
//                    return $dataSearch[0]->network_offer_id;
//                } else if($countSearch > 1) {
//                    $this->result['offer']['dublicate'][$lt_Campaign_id] = [
//                        'name' => $lt_Campaign_name,
//                        $date => [
//                            'click' => (string) $lt_Statistic->Clicks,
//                            'revenue' => (string) $lt_Statistic->Revenue]
//                    ];
//                } else {
//                    $this->result['offer']['not_found'][$lt_Campaign_id] = [
//                        'name' => $lt_Campaign_name,
//                        $date => [
//                            'click' => (string) $lt_Statistic->Clicks,
//                            'revenue' => (string) $lt_Statistic->Revenue]
//                    ];
//                }
//            }

        } else {
            $this->result['offer']['not_found'][$lt_Campaign_id] = [
                'name' => $lt_Campaign_name,
                $date => [
                    'click' => (string) $lt_Statistic->Clicks,
                    'revenue' => (string) $lt_Statistic->Revenue]
            ];
        }
    }


    protected function loadEFData()
    {
        $this->loadAffiliate();
        $this->loadOffer();
    }

}