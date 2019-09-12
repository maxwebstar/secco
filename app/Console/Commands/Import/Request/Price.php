<?php

namespace App\Console\Commands\Import\Request;

use Illuminate\Console\Command;
use DB;
use Validator;
use PDOException;
use Exception;

use App\Models\Request\Price as modelRequestPrice;
use App\Models\User as modelUser;
use App\Models\Offer as modelOffer;
use App\Models\Affiliate as modelAffiliate;
use App\Models\Network as modelNetwork;


class Price extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-mongo:request-price {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import price request from mongo db to mysql';

    /**
     *
     * Type command for import advertiser
     *
     * - import
     * - addition
     * - investigate
     */
    protected $type_command;

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
            case "investigate" :
                $this->investigate();
                break;
            default :
                throw new Exception('Empty type commaand for import.');
                break;
        }
    }


    protected function import()
    {
        $result = [
            'create' => 0,
            'offer_id_empty' => 0,
            'offer_not_found' => 0,
            'affiliate_not_found' => 0
        ];

        $dataNetwork = modelNetwork::where('short_name', 'LT')->first();

        $mongoDB = DB::connection('mongodb');
        $mongoDB->collection('activity')
            ->where('type', "pricerequest")
            ->orderBy('time')
            ->chunk(100, function ($arrPrice) use ($mongoDB, $dataNetwork, &$result) {

                foreach($arrPrice as $iterPrice){

                    if(empty($iterPrice['cid'])){
                        $result['offer_id_empty'] ++;
                        continue;
                    }

                    $mongo_id = (string) $iterPrice['_id'];

                    $exist = modelRequestPrice::where('mongo_id', $mongo_id)->first();
                    if(!$exist){

                        $dataOffer = modelOffer::where('lt_id', $iterPrice['cid'])->first();
                        if($dataOffer){

                            $data = new modelRequestPrice();

                            if($iterPrice['affiliate']){
                                if($iterPrice['affiliate'] == "all"){
                                    $data->affiliate_all = 1;
                                } else {
                                    $dataAffiliate = modelAffiliate::where('lt_id', $iterPrice['affiliate'])->first();
                                    if(!$dataAffiliate){
                                        $result['affiliate_not_found'] ++;
                                        continue;
                                    }
                                    $data->affiliate_id = $dataAffiliate->id;
                                }
                            }

                            if(isset($iterPrice['time']) && $iterPrice['time']) {
                                $created = date('Y-m-d H:i:s', $iterPrice['time']);
                            } else {
                                $created = date('Y-m-d H:i:s');
                            }

                            $data->fill([
                                'network_id' => $dataNetwork->id,
                                'offer_id' => $dataOffer->id,
                                'date' => date('Y-m-d', $iterPrice['edate']),
                                'reason' => $iterPrice['reason'] ? : null,
                                'updated_at' => $created,
                                'created_at' => $created,
                                'mongo_id' => $mongo_id,
                            ]);

                            $priceIn = trim($iterPrice['newin']);
                            $priceOut = trim($iterPrice['newout']);

                            $priceCurrentIn = trim($iterPrice['currentInPrice']);
                            $priceCurrentOut = trim($iterPrice['currentOutPrice']);

                            $data->price_in = $priceIn;
                            $data->price_out = $priceOut;
                            $data->current_price_in = $priceCurrentIn;
                            $data->current_price_out = $priceCurrentOut;

                            if(isset($iterPrice['ptype'])){
                                switch($iterPrice['ptype']){
                                    case "Price Increase" :
                                        $data->type = 1;
                                        break;
                                    case "Price Decrease" :
                                        $data->type = 2;
                                        break;
                                }
                            }

                            if(isset($iterPrice['changecap']) && $iterPrice['changecap'] == "yes"){
                                $data->cap_change = 1;
                            }

                            switch($iterPrice['approvalStatus']){
                                case "Approved" :
                                    $data->status = 3;
                                    break;
                                case "Declined" :
                                    $data->status = 2;
                                    break;
                            }

                            $dataUser = modelUser::where('mongo_user_id', $iterPrice['creatorId'])->first();
                            if($dataUser){
                                $data->created_by = $dataUser->email;
                                $data->created_by_id = $dataUser->id;
                            } else {
                                $data->created_by = $iterPrice['creatorName'];
                                $data->created_by_id = 0;
                            }

                            try {

                                $data->save();

                            } catch (PDOException $e) {
                                var_dump($e->getMessage());
                                dd($iterPrice);
                            } catch (Exception $e){
                                var_dump($e->getMessage());
                                dd($iterPrice);
                            }

                            $result['create'] ++;

                        } else {

                            $result['offer_not_found'] ++;
                        }
                    }
                }
            });

        var_dump($result);
    }


    protected function investigate()
    {
        $result = [];
        $count = 0;

        $mongoDB = DB::connection('mongodb');
        $mongoDB->collection('activity')
            ->where('type', 'pricerequest')
            ->orderBy('time')
            ->chunk(100, function ($arrPrice) use (&$result, &$count) {

                foreach ($arrPrice as $price) {

                    foreach($price as $key => $value){

                        if(empty($result[$key])){
                            $result[$key] = $value;
                        }
                    }

                    $count ++;
                }

            });

        var_dump('count ' . $count);
        var_dump($result);
    }

}