<?php

namespace App\Console\Commands\EverFlow;

use Illuminate\Console\Command;

use App\Models\Offer as modelOffer;
use App\Models\User as modelUser;
use App\Models\Advertiser as modelAdvertiser;
use App\Models\CampaignType as modelCampaignType;
use App\Models\OfferCategory as modelOfferCategory;
use App\Models\Pixel as modelPixel;
use App\Models\Domain as modelDomain;

use DB;
use Exception;

use App\Services\EverFlow\General as EF_General;
use App\Services\EverFlow\Offer as EF_Offer;

class Offer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'everflow:offer {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronization of data';

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
            'exist' => 0,
            'create' => 0,
            'skip' => 0,
            'update' => 0,
            'all' => 0,
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
            case "sync" :
                $this->sync();
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

        $ef_Offer = new EF_Offer();

        $efResponse = $ef_Offer->getAllOffer($page, $page_size);

        if($efResponse->offers){

            var_dump($efResponse->paging);

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
                    var_dump('page ' . $page);
                }
            }
        }

        var_dump($this->result);
    }


    protected function cycleOffer($data)
    {

        foreach($data as $iterOffer){

            if($iterOffer->offer_status == "deleted"){
                continue;
            }

            $existOffer = modelOffer::where('ef_id', $iterOffer->network_offer_id)->first();
            if(!$existOffer){

                $updateOffer = modelOffer::where('campaign_name', $iterOffer->name)->where('ef_id', 0)->first();
                if($updateOffer){

                    $updateOffer->need_api_ef = 1;
                    $updateOffer->ef_id = $updateOffer->network_offer_id;
                    $updateOffer->save();

                    $this->result['skip'] ++;

                    continue;
                }

                $newOffer = new modelOffer();
                $dataOffer = $newOffer;
                $dataOffer->fill([
                    'campaign_name' => $iterOffer->name,
                    'campaign_link' => $iterOffer->destination_url,

                    //'geos' => isset($iterOffer['geos']) ? $iterOffer['geos'] : null,
                    //'geo_redirect_url' => isset($iterOffer['geoRedirectUrl']) ? $iterOffer['geoRedirectUrl'] : null,

                    'accepted_traffic' => $iterOffer->html_description,
                    'internal_note' => $iterOffer->internal_notes,
                ]);

                $dataCampaignType = modelCampaignType::where('ef_key', $iterOffer->relationship->payout_revenue->entries[0]->payout_type)->first();
                if($dataCampaignType){
                    $dataOffer->campaign_type = $dataCampaignType->key;
                    $dataOffer->price_in = $iterOffer->relationship->payout_revenue->entries[0]->payout_amount;
                    $dataOffer->price_out = $iterOffer->relationship->payout_revenue->entries[0]->revenue_amount;

                }

                $dataAdvertiser = modelAdvertiser::where('ef_id', $iterOffer->network_advertiser_id)->first();
                if($dataAdvertiser){
                    $dataOffer->advertiser_id = $dataAdvertiser->id;
                    $dataOffer->advertiser_contact = $dataAdvertiser->contect;
                    $dataOffer->advertiser_email = $dataAdvertiser->email;
                }

                $dataOffer->created_by = "import from ef";

                $dataCategory = modelOfferCategory::where('ef_id', $iterOffer->network_category_id)->first();
                if($dataCategory){
                    $dataOffer->offer_category_id = $dataCategory->id;
                }

                $dataPixel = modelPixel::where('ef_key', $iterOffer->conversion_method)->first();
                if($dataPixel){
                    $dataOffer->pixel_id = $dataPixel->id;
                }

                $dataDomain = modelDomain::where('ef_id', $iterOffer->network_tracking_domain_id)->first();
                if($dataDomain){
                    $dataOffer->domain_id = $dataDomain->id;
                }

                $dataOffer->need_api_ef = 1;
                $dataOffer->ef_id = $iterOffer->network_offer_id;
                $dataOffer->ef_status = $iterOffer->offer_status;
                $dataOffer->status = 3;

                $dataOffer->created_at = date('Y-m-d H:i:s', $iterOffer->time_created);
                $dataOffer->updated_at = date('Y-m-d H:i:s', $iterOffer->time_saved);

                DB::beginTransaction();

                try {

                    $dataOffer->save();
                    //$this->importCreative($iterOffer, $dataOffer);

                    DB::commit();

                    $this->result['create'] ++;

                } catch (Exception $e) {
                    var_dump($e->getMessage());
                    DB::rollBack();
                    dd($iterOffer);
                    exit();
                } catch (PDOException $e) {
                    var_dump($e->getMessage());
                    DB::rollBack();
                    dd($iterOffer);
                    exit();
                }

            } else {
                $this->result['exist'] ++;
            }

        }
    }

    protected function sync()
    {
        $page = 1;
        $page_size = 100;

        $ef_Offer = new EF_Offer();

        $efResponse = $ef_Offer->getAllOffer($page, $page_size);

        if($efResponse->offers){

            var_dump($efResponse->paging);

            $this->cycleOfferSync($efResponse->offers);

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
                    $this->cycleOfferSync($efResponse->offers);
                    var_dump('page ' . $page);
                }
            }
        }

        var_dump($this->result);
    }


    protected function cycleOfferSync($data)
    {
        foreach($data as $iterOffer)
        {
            if($iterOffer->offer_status == "deleted"){
                continue;
            }

            $this->result['all'] ++;

            $existOffer = modelOffer::where('ef_id', $iterOffer->network_offer_id)->first();
            if($existOffer){

                $dataAdvertiser = $existOffer->advertiser;

                if(isset($iterOffer->payout_revenue->entries[0]->revenue_amount)){

                    if($dataAdvertiser->currency_id == 2 || $dataAdvertiser->currency_id == 3){

                        $dataCurrency = $dataAdvertiser->currency;
                        $price_in = round(($iterOffer->payout_revenue->entries[0]->revenue_amount / $dataCurrency->rate), 2);

                        var_dump("price_in_old " . $price_in);
                        var_dump("price_in " . $price_in);

                    } else {
                        $price_in = $iterOffer->payout_revenue->entries[0]->revenue_amount;
                    }

                    $existOffer->price_in = $price_in;
                }

                if(isset($iterOffer->payout_revenue->entries[0]->payout_amount)){

                    if($dataAdvertiser->currency_id == 2 || $dataAdvertiser->currency_id == 3){

                        $dataCurrency = $dataAdvertiser->currency;
                        $price_out = round(($iterOffer->payout_revenue->entries[0]->payout_amount / $dataCurrency->rate), 2);

                        var_dump("price_out_old " . $price_out);
                        var_dump("price_out " . $price_out);

                    } else {
                        $price_out = $iterOffer->payout_revenue->entries[0]->payout_amount;
                    }

                    $existOffer->price_out = $price_out;
                }

                $existOffer->ef_status = $iterOffer->offer_status;
                $existOffer->save();

                $this->result['update'] ++;
            }
        }
    }
}