<?php

namespace App\Console\Commands\Cron\EverFlow;

use Illuminate\Console\Command;

use App\Models\OfferAffiliate as modelOfferAffiliate;
use App\Models\Offer as modelOffer;
use App\Models\Affiliate as modelAffiliate;
use App\Models\Network as modelNetwork;

use App\Services\EverFlow\Offer as EF_Offer;

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
    protected $signature = 'cron/everflow:offer {type} {param1?} {param2?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronization of offer';

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
            'count' => 0,
            'total' => 0,
            'create' => 0,
            'not_found' => 0,
            'exist' => 0,
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
            case "connect_to_affiliate" :
                $this->connectToAffiliate();
                break;
            case "import" :
                $this->import();
                break;
            case "sync-statistic-yesterday" :
                $this->syncStatisticYesterday();
                break;
            case "sync-statistic-manual" :
                $this->syncStatisticManual();
                break;
            default :
                throw new Exception('Empty type commaand.');
                break;
        }
    }


    protected function import()
    {
        $page = 1;
        $page_size = 100;

        $EF_Offer = new EF_Offer();

        $efResponse =  $EF_Offer->getAllOffer($page, $page_size);
        if($efResponse->offers){

            var_dump("page $page");

            $this->cycle($efResponse->offers);

            $total = $efResponse->paging->total_count;
            if($total > $page_size){
                $page_last = ceil(($total/$page_size));
            } else {
                $page_last = 1;
            }

            $this->result['total'] = $total;

            while($page_last > $page){
                $page ++;
                $efResponse = $EF_Offer->getAllOffer($page, $page_size);

                if($efResponse->offers){

                    var_dump("page $page");

                    $this->cycle($efResponse->offers);
                }
            }
        }

        var_dump($this->result);
    }



    protected function cycle($efOffer)
    {
        foreach ($efOffer as $iterOffer) {

            $exist = modelOffer::where('ef_id', $iterOffer->network_offer_id)->first();
            if(!$exist){

                $this->result['not_found'] ++;
            }

            $this->result['count'] ++;
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

            $dateStart = $param1;
            $dateEnd = $param2;

        } else {

            $tmpStart = new DateTime();
            $tmpStart->modify('first day of this month');
            $dateStart = $tmpStart->format('Y-m-d');

            $tmpEnd = new DateTime();
            $dateEnd = $tmpEnd->format('Y-m-d');
        }

        $dataNetwork = modelNetwork::where('short_name', 'EF')->first();
        if(!$dataNetwork){
            throw new Exception('Network not found.');
        }

        $ef_Offer = new EF_Offer();
        $dataStat = $ef_Offer->getStatByAffiliate($dateStart, $dateEnd);

        if($dataStat){
            foreach($dataStat->table as $iterStat){

                $network_offer_id = $iterStat->columns[0]->id;
                $network_affiliate_id = $iterStat->columns[1]->id;

                $exist = modelOfferAffiliate::where('network_id', $dataNetwork->id)
                    ->where('network_offer_id', $network_offer_id)
                    ->where('network_affiliate_id', $network_affiliate_id)
                    ->first();

                if(!$exist){

                    $dataOffer = modelOffer::where('ef_id', $network_offer_id)->first();
                    $dataAffiliate = modelAffiliate::where('ef_id', $network_affiliate_id)->first();

                    if($dataOffer && $dataAffiliate) {

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
                        if(!$dataOffer){
                            $result['not_found_offer'] ++;
                        }
                        if(!$dataAffiliate){
                            $result['not_found_affiliate'] ++;
                        }
                    }

                } else {
                    $exist->touch();
                    $result['update'] ++;
                }
            }
        }
    }

}