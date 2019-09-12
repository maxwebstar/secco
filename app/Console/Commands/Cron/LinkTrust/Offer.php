<?php

namespace App\Console\Commands\Cron\LinkTrust;

use Illuminate\Console\Command;

use App\Models\OfferAffiliate as modelOfferAffiliate;
use App\Models\Offer as modelOffer;
use App\Models\Affiliate as modelAffiliate;
use App\Models\Network as modelNetwork;

use App\Services\LinkTrust\Offer as LT_Offer;

use DateTime;
use DateTimeZone;
use Exception;

class Offer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron/linktrust:offer {type} {param1?} {param2?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronization of offer';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
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
            case "connect_to_affiliate" :
                $this->connectToAffiliate();
                break;
            default :
                throw new Exception('Empty type commaand.');
                break;
        }
    }


    protected function connectToAffiliate()
    {
        $result = [
            'create' => 0,
            'update' => 0,
            'not_found_offer' => 0,
            'not_found_affiliate' => 0,
        ];

        $param1 = $this->argument('param1');
        $param2 = $this->argument('param2');

        if(DateTime::createFromFormat('Y-m-d', $param1) !== FALSE &&
            DateTime::createFromFormat('Y-m-d', $param2) !== FALSE &&
            strtotime($param1) < strtotime($param2)){

            $dateStart = new DateTime($param1);
            $dateEnd = new DateTime($param2);

        } else {

            $dateStart = new DateTime();
            $dateStart->modify('first day of this month');

            $dateEnd = new DateTime();
        }

        while($dateStart < $dateEnd){

            $dateCurrent = $dateStart->format('n/j/Y');

            $this->loadData($dateCurrent, $result);

            $dateStart->modify('+1 day');
        }

        var_dump($result);
    }


    protected function loadData($date, &$result)
    {
        var_dump($date);

        $dataNetwork = modelNetwork::where('short_name', 'LT')->first();
        if(!$dataNetwork){
            throw new Exception('Network not found.');
        }

        $lt_Offer = new LT_Offer();
        $dataStat = $lt_Offer->getStat($date, $date);

        if($dataStat){
            foreach($dataStat->Campaign as $iterStat){

                $attrOffer = $iterStat->attributes();
                $network_offer_id = (string) $attrOffer['Id'];

                $dataOffer = modelOffer::where('lt_id', $network_offer_id)->first();
                if(!$dataOffer){
                    $result['not_found_offer'] ++;
                    continue;
                }

                if(isset($iterStat->Affiliates->Affiliate) && count($iterStat->Affiliates->Affiliate)){
                    foreach($iterStat->Affiliates->Affiliate as $iterAff){

                        $attrAff = $iterAff->attributes();
                        $network_affiliate_id = (string) $attrAff['Id'];

                        $dataAffiliate = modelAffiliate::where('lt_id', $network_affiliate_id)->first();
                        if($dataAffiliate){

                            $exist = modelOfferAffiliate::where('network_id', $dataNetwork->id)
                                ->where('network_offer_id', $network_offer_id)
                                ->where('network_affiliate_id', $network_affiliate_id)
                                ->first();

                            if(!$exist){

                                $data = new modelOfferAffiliate();
                                $data->fill([
                                    'network_id' => $dataNetwork->id,
                                    'offer_id' => $dataOffer->id,
                                    'affiliate_id' => $dataAffiliate->id,
                                    'network_offer_id' => $network_offer_id,
                                    'network_affiliate_id' => $network_affiliate_id,
                                ]);
                                $data->save();
                                $result['create'] ++;

                            } else {
                                $exist->touch();
                                $result['update'] ++;
                            }

                        } else {
                            $result['not_found_offer'] ++;
                        }
                    }
                }
            }
        }
    }

}
