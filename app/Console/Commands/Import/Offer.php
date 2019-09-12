<?php

namespace App\Console\Commands\Import;

use Illuminate\Console\Command;
use DB;
use Validator;
use PDOException;
use Exception;

use App\Models\Offer as modelOffer;
use App\Models\OfferCategory as modelCategory;
use App\Models\OfferCreative as modelCreative;
use App\Models\Pixel as modelPixel;
use App\Models\CampaignType as modelCampaignType;
use App\Models\CapType as modelCapType;
use App\Models\CapUnit as modelCapUnit;
use App\Models\Domain as modelDomain;
use App\Models\User as modelUser;
use App\Models\Advertiser as modelAdvertiser;

class Offer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-mongo:offer {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import offers from mongo to mysql';

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
            case "import" :
                $this->import();
                break;
            case "check-campaign" :
                $this->checkCampaign();
                break;
            default :
                throw new Exception('Empty type commaand for import.');
                break;
        }
    }


    public function import()
    {
        $result = ['create' => 0, 'update' => 0];

        $mongoDB = DB::connection('mongodb');
        $mongoDB->collection('activity')
            ->where('type', "newoffer")
            ->orderBy('time')
            ->chunk(100, function ($arrOffer) use ($mongoDB, &$result) {

                foreach($arrOffer as $iterOffer){

                    $mongo_id = (string) $iterOffer['_id'];

                    $existOffer = modelOffer::where('mongo_id', $mongo_id)->first();
                    if(!$existOffer){

                        $newOffer = new modelOffer();
                        $dataOffer = $newOffer;
                        $dataOffer->fill([
                            'campaign_name' => $iterOffer['campname'],
                            'campaign_link' => $iterOffer['link'],
                            'advertiser_contact' => isset($iterOffer['advertiserContact']) ? $iterOffer['advertiserContact'] : null,
                            'advertiser_email' => isset($iterOffer['advertiserEmail']) ? $iterOffer['advertiserEmail'] : null,
                            'pixel_location' => $iterOffer['pixel_location'],
                            'geos' => isset($iterOffer['geos']) ? $iterOffer['geos'] : null,
                            'geo_redirect_url' => isset($iterOffer['geoRedirectUrl']) ? $iterOffer['geoRedirectUrl'] : null,
                            'accepted_traffic' => isset($iterOffer['acceptedTraffic']) ? $iterOffer['acceptedTraffic'] : null,
                            'affiliate_note' => isset($iterOffer['affiliatesNotes']) ? $iterOffer['affiliatesNotes'] : null,
                            'internal_note' => isset($iterOffer['internalNotes']) ? $iterOffer['internalNotes'] : null,
                            'mongo_id' => $mongo_id,
                        ]);

                        if($iterOffer['campaignType']){
                            $campaignType = modelCampaignType::where('key', $iterOffer['campaignType'])->first();
                            if($campaignType){
                                $dataOffer->campaign_type = $campaignType->key;
                            }
                        }
                        if(isset($iterOffer['manager'])){
                            $manager = modelUser::where('name', $iterOffer['manager'])->first();
                            if($manager){
                                $dataOffer->manager_id = $manager->id;
                            }
                        }
                        if(isset($iterOffer['advertiser']) && isset($iterOffer['advertiser']['mid'])){
                            $advertiser =  modelAdvertiser::where('lt_id', $iterOffer['advertiser']['mid'])->first();
                            if($advertiser){
                                $dataOffer->advertiser_id = $advertiser->id;
                            }
                        }
                        if(isset($iterOffer['creatorId'])){
                            $createdBy = modelUser::where('mongo_user_id', $iterOffer['creatorId'])->first();
                            if($createdBy){
                                $dataOffer->created_by = $createdBy->email;
                                $dataOffer->created_by_id = $createdBy->id;
                            }
                        }
                        if(isset($iterOffer['offerType'])){
                            $category = modelCategory::where('name', $iterOffer['offerType'])->first();
                            if($category){
                                $dataOffer->offer_category_id = $category->id;
                            }
                        }
                        if(isset($iterOffer['pixelType'])){

                            if($iterOffer['pixelType'] == "Javasript"){
                                $iterOffer['pixelType'] = "JavaScript";
                            }

                            $pixel = modelPixel::where('key', $iterOffer['pixelType'])->first();
                            if($pixel){
                                $dataOffer->pixel_id = $pixel->id;
                            }
                        }
                        if(isset($iterOffer['redirect'])){
                            $dataOffer->redirect = $iterOffer['redirect'] == "Y" ? 1 : 0;
                        }
                        if(isset($iterOffer['redirecturl'])){
                            $dataOffer->redirect_url = $iterOffer['redirecturl'];
                        }
                        if(isset($iterOffer['domain'])){
                            $domain = modelDomain::where('value', $iterOffer['domain'])->first();
                            if($domain){
                                $dataOffer->domain_id = $domain->id;
                            }
                        }

                        if(isset($iterOffer['capType'])){
                            $capType = modelCapType::where('key', $iterOffer['capType'])->first();
                            if($capType){
                                $dataOffer->cap_type_id = $capType->id;
                            }
                        }
                        if(isset($iterOffer['leadCapType'])){
                            $capUnit = modelCapUnit::where('key', $iterOffer['leadCapType'])->first();
                            if($capUnit){
                                $dataOffer->cap_unit_id = $capUnit->id;
                            }
                        }
                        if(isset($iterOffer['newin'])){
                            $iterOffer['newin'] = str_replace(['€', '$', ','], "", $iterOffer['newin']);
                            $dataOffer->price_in = $iterOffer['newin'];
                        }
                        if(isset($iterOffer['newout'])){
                            $iterOffer['newout'] = str_replace(['€', '$', ','], "", $iterOffer['newout']);
                            $dataOffer->price_out = $iterOffer['newout'];
                        }
                        if(isset($iterOffer['monetarycap'])){
                            $dataOffer->cap_monetary = $iterOffer['monetarycap'];
                        }
                        if(isset($iterOffer['leadcap'])){
                            $dataOffer->cap_lead = $iterOffer['leadcap'];
                        }

                        if(isset($iterOffer['approvalStatus'])){
                            switch($iterOffer['approvalStatus']){
                                case 'Approved' :
                                    $dataOffer->status = 3;
                                    break;
                                case 'Declined' :
                                    $dataOffer->status = 2;
                                    break;
                                case 'pending' :
                                    $dataOffer->status = 1;
                                    break;
                            }
                        }

                        if(isset($iterOffer['campaign']) && isset($iterOffer['campaign']['cid'])){
                            if($iterOffer['campaign']['cid']){
                                $dataOffer->need_api_lt = 1;
                                $dataOffer->lt_id = $iterOffer['campaign']['cid'];
                            }

                            $campaign = $mongoDB->collection('campaigns')->where('lt_id', $iterOffer['campaign']['cid'])->first();
                            if($campaign){
                                $mongo_campaign_id = (string) $campaign['_id'];
                                $dataOffer->mongo_campaign_id = $mongo_campaign_id;
                            }
                        }

                        if(isset($iterOffer['time']) && $iterOffer['time']){
                            $created = date('Y-m-d H:i:s', $iterOffer['time']);
                        } else {
                            $created = date('Y-m-d H:i:s');
                        }
                        $dataOffer->created_at = $created;
                        $dataOffer->updated_at = $created;


                        DB::beginTransaction();

                        try {

                            $dataOffer->save();

                            $this->importCreative($iterOffer, $dataOffer);

                            DB::commit();

                            $result['create'] ++;

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

                        if(!$existOffer->lt_id) {

                            if (isset($iterOffer['campaign']) &&
                                isset($iterOffer['campaign']['cid']) &&
                                $iterOffer['campaign']['cid']
                            ) {

                                $existOffer->need_api_lt = 1;
                                $existOffer->lt_id = $iterOffer['campaign']['cid'];
                                $existOffer->save();

                                $result['update']++;

                            } else {

                                $dataCampaign = $mongoDB->collection('campaigns')
                                    ->where('name', $iterOffer['campname'])->get();

                                $count = count($dataCampaign);
                                if($count == 1){

                                    $existOffer->need_api_lt = 1;
                                    $existOffer->lt_id = $dataCampaign[0]['lt_id'];
                                    $existOffer->save();

                                    $result['update']++;
                                }
                            }
                        }
                    }
                }

        });

        var_dump($result);
    }

    protected function importCreative($iterOffer, $dataOffer)
    {
        for($i = 1; $i <= 50; $i++) {

            if (isset($iterOffer['creative_name_' . $i])) {

                $creativeName = $iterOffer['creative_name_' . $i];
                $creativeLink = $iterOffer['link_' . $i];
                $creativePriceIN = $iterOffer['inprice_' . $i];
                $creativePriceOut = $iterOffer['outprice_' . $i];

                $existCreative = modelCreative::where('mongo_offer_id', $dataOffer->mongo_id)
                    ->where('name', $creativeName)
                    ->where('iteration', $i)
                    ->first();

                if ($existCreative) {
                    /*$dataCreative = $existCreative;
                     $existCreative->fill([
                        'link' => $creativeLink,
                        'price_in' => $creativePriceIN,
                        'price_out' => $creativePriceOut,
                    ]);*/
                } else {
                    $dataCreative = new modelCreative();
                    $dataCreative->fill([
                        'offer_id' => $dataOffer->id,
                        'iteration' => $i,
                        'name' => $creativeName,
                        'link' => $creativeLink,
                        'price_in' => $creativePriceIN,
                        'price_out' => $creativePriceOut,
                        'status' => $dataOffer->status,
                        'created_at' => $dataOffer->created_at,
                        'mongo_offer_id' => $dataOffer->mongo_id,
                    ]);
                }

                $dataCreative->save();
            }
        }
    }


    protected function checkCampaign()
    {
        $result = ['count' => 0, 'by_name' => 0, 'id' => []];

        $mongoDB = DB::connection('mongodb');
        $mongoDB->collection('campaigns')
            ->orderBy('lastUpdated')
            ->where('lt_id', '>', 0)
            ->chunk(100, function ($arrCampaign) use ($mongoDB, &$result) {

                foreach($arrCampaign as $iter){

                    try {

                        $exist = modelOffer::where('lt_id', $iter['lt_id'])->first();
                        if (!$exist) {

                            $byName = modelOffer::where('campaign_name', $iter['name'])->first();
                            if ($byName) {
                                $result['by_name']++;
                            } else {
                                $result['count']++;
                                $result['id'][] = $iter['lt_id'];
                            }
                        }

                    } catch (Exception $e) {
                        var_dump($e->getMessage());
                        var_dump($iter);
                        exit();
                    } catch (PDOException $e) {
                        var_dump($e->getMessage());
                        var_dump($iter);
                        exit();
                    }
                }
        });

        var_dump($result);
    }

}