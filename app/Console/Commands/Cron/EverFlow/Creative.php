<?php

namespace App\Console\Commands\Cron\EverFlow;

use Illuminate\Console\Command;

use App\Services\EverFlow\Offer as EF_Offer;
use App\Services\EverFlow\Creative as EF_Creative;

use App\Models\Offer as modelOffer;
use App\Models\OfferCreative as modelOfferCreative;
use App\Models\OfferCreativeMissing as modelOfferCreativeMis;

use DateTime;
use DateTimeZone;
use Exception;

class Creative extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron/everflow:creative {type} {param1?} {param2?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronization of creative';

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
            'total' => 0,
            'count' => 0,
            'create' => 0,
            'exist' => 0,
            'create_missing' => 0,
            'exist_missing' => 0,
            'offer_not_exist' => 0,
            'creative_exist' => 0,
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
            case "import" :
                $this->import();
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

        $EF_Creative = new EF_Creative();

        $efResponse =  $EF_Creative->getAllCreative($page, $page_size);
        if($efResponse->creatives){

            var_dump("page $page");

            $this->cycle($efResponse->creatives);

            $total = $efResponse->paging->total_count;
            if($total > $page_size){
                $page_last = ceil(($total/$page_size));
            } else {
                $page_last = 1;
            }

            $this->result['total'] = $total;

            while($page_last > $page){
                $page ++;
                $efResponse = $EF_Creative->getAllCreative($page, $page_size);

                if($efResponse->creatives){

                    var_dump("page $page");

                    $this->cycle($efResponse->creatives);
                }
            }
        }

        var_dump($this->result);
    }


    protected function cycle($efCreative)
    {
        foreach ($efCreative as $iterCre) {

            $dataOffer = modelOffer::where('ef_id', $iterCre->network_offer_id)->first();
            if($dataOffer){

                $dataIteration = modelOfferCreative::where('offer_id', $dataOffer->id)->orderBy('iteration', 'DESC')->first();
                if($dataIteration){
                    $this->addMissing($iterCre, $dataOffer);
                } else {
                    $this->add($iterCre, $dataOffer);
                }

            } else {
                $this->result['offer_not_exist'] ++;
            }

            $this->result['count'] ++;
        }
    }


    protected function add($iterCre, $dataOffer)
    {
        $dataExist = modelOfferCreative::where('ef_id', $iterCre->network_offer_creative_id)->first();
        if(!$dataExist){
            $dataSearch = modelOfferCreative::where('name', $iterCre->name)->where('ef_id', 0)->get();
            if($dataSearch->count() == 1){
                $dataExist = $dataSearch[0];
            }
        }

        if(!$dataExist){

            $dataIteration = modelOfferCreative::where('offer_id', $dataOffer->id)->orderBy('iteration', 'DESC')->first();
            if($dataIteration){
                $iteration = $dataIteration->iteration + 1;
            } else {
                $iteration = 1;
            }

            $data = new modelOfferCreative();
            $data->fill([
                'offer_id' => $dataOffer->id,
                'iteration' => $iteration,
                'name' => $iterCre->name,
                'link' => $iterCre->resource_url,
                'price_in' => null,
                'price_out' => null,
                'lt_id' => 0,
                'ef_id' => $iterCre->network_offer_creative_id,
                'ef_status' => $iterCre->creative_status,
                'status' => 3,
                'updated_at' => $iterCre->time_saved ? date('Y-m-d H:i:s', $iterCre->time_saved) : date('Y-m-d H:i:s', $iterCre->time_created),
                'created_at' => date('Y-m-d H:i:s', $iterCre->time_created),
            ]);
            $data->save();

            $this->result['create'] ++;

        } else {

            if(!$dataExist->ef_id){
                $dataExist->ef_id = $iterCre->network_offer_id;
            }
            if(!$dataExist->offer_id){
                $dataOffer = modelOffer::where('ef_id', $iterCre->network_offer_id)->first();
                if($dataOffer){
                    $dataExist->offer_id = $dataOffer->id;
                }
            }
            $dataExist->ef_status = $iterCre->creative_status;
            $dataExist->save();

            $this->result['exist'] ++;
        }
    }


    protected function addMissing($iterCre, $dataOffer)
    {
        $dataCreative = modelOfferCreative::where('ef_id', $iterCre->network_offer_creative_id)->first();
        if(!$dataCreative){

            $dataExist = modelOfferCreativeMis::where('ef_id', $iterCre->network_offer_creative_id)->first();
            if(!$dataExist){

                $data = new modelOfferCreativeMis();
                $data->fill([
                    'offer_id' => $dataOffer->id,
                    'name' => $iterCre->name,
                    'link' => $iterCre->resource_url,
                    'price_in' => null,
                    'price_out' => null,
                    'lt_id' => 0,
                    'ef_id' => $iterCre->network_offer_creative_id,
                    'ef_status' => $iterCre->creative_status,
                    'status' => 1,
                    'updated_at' => $iterCre->time_saved ? date('Y-m-d H:i:s', $iterCre->time_saved) : date('Y-m-d H:i:s', $iterCre->time_created),
                    'created_at' => date('Y-m-d H:i:s', $iterCre->time_created),
                ]);
                $data->save();

                $this->result['create_missing'] ++;

            } else {

                if(!$dataExist->offer_id){
                    $dataOffer = modelOffer::where('ef_id', $iterCre->network_offer_id)->first();
                    if($dataOffer){
                        $dataExist->offer_id = $dataOffer->id;
                    }
                }
                $dataExist->ef_status = $iterCre->creative_status;
                $dataExist->save();

                $this->result['exist_missing'] ++;
            }

        } else {
            $this->result['creative_exist'] ++;
        }
    }
}