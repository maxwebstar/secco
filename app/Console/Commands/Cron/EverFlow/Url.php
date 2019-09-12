<?php
namespace App\Console\Commands\Cron\EverFlow;

use Illuminate\Console\Command;

use App\Models\Offer as modelOffer;
use App\Models\OfferUrl as modelOfferUrl;
use App\Models\Affiliate as modelAffiliate;
use App\Models\Network as modelNetwork;

use App\Services\EverFlow\Offer as EF_Offer;

use DateTime;
use DateTimeZone;
use Exception;

class Url extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron/everflow:url {type} {param1?} {param2?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronization of url';

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
            'offer_count' => 0,
            'offer_not_found' => 0,
            'url_count' => 0,
            'url_create' => 0,
            'url_update' => 0,
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

        $EF_Offer = new EF_Offer();

        $efResponse = $EF_Offer->getAllOffer($page, $page_size);
        if ($efResponse->offers) {

            var_dump("page $page");

            $this->cycle($efResponse->offers);

            $total = $efResponse->paging->total_count;
            if ($total > $page_size) {
                $page_last = ceil(($total / $page_size));
            } else {
                $page_last = 1;
            }

            $this->result['total'] = $total;

            while ($page_last > $page) {
                $page++;
                $efResponse = $EF_Offer->getAllOffer($page, $page_size);

                if ($efResponse->offers) {

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

            $EF_Offer = new EF_Offer();
            $efOffer = $EF_Offer->getOffer($iterOffer->network_offer_id, "urls");

            if(isset($efOffer->relationship->urls) && $efOffer->relationship->urls->total){

                $dataOffer = modelOffer::where('ef_id', $iterOffer->network_offer_id)->first();
                if ($dataOffer) {

                    foreach($efOffer->relationship->urls->entries as $url){

                        $data = modelOfferUrl::where('offer_id', $dataOffer->id)->where('ef_id', $url->network_offer_url_id)->first();
                        if($data){
                            $data->url = $url->destination_url;
                            $data->ef_status = $url->url_status;

                            $this->result['url_update']++;
                        } else {
                            $data = new modelOfferUrl();
                            $data->offer_id = $dataOffer->id;
                            $data->name = $url->name;
                            $data->url = $url->destination_url;
                            $data->ef_id = $url->network_offer_url_id;
                            $data->ef_status = $url->url_status;

                            $this->result['url_create']++;
                        }

                        $data->save();

                        $this->result['url_count']++;
                    }

                } else {
                    $this->result['offer_not_found']++;
                    continue;
                }
            }

            $this->result['offer_count']++;
        }

    }

}