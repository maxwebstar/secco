<?php

namespace App\Console\Commands\Import;

use Illuminate\Console\Command;

use App\Models\User as modelUser;
use App\Models\Country as modelCountry;
use App\Models\State as modelState;
use App\Models\Affiliate as modelAffiliate;

use DB;
use Validator;
use PDOException;
use Exception;


class Affiliate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-mongo:affiliate {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import affiliate from mongo db to mysql';

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

        switch($type){
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
        $result = ['create' => 0];

        $mongoDB = DB::connection('mongodb');
        $mongoDB->collection('affiliates')
            ->where('name', '>', "")
            ->orderBy('lt_id')
            ->chunk(100, function ($arrAffiliate) use (&$result) {

                foreach ($arrAffiliate as $iterAff) {

                    $mongo_id = (string) $iterAff['_id'];

                    $modelAdvertiser = new modelAffiliate();
                    $exist = $modelAdvertiser->where('mongo_id', $mongo_id)->orWhere('lt_id', $iterAff['lt_id'])->first();

                    if(!$exist){

                        try {

                            $data = new modelAffiliate();
                            $data->fill([
                                'name' => $iterAff['name'],
                                'email' => isset($iterAff['email']) ? $iterAff['email'] : null,
                                'lt_id' => $iterAff['lt_id'],
                                'mongo_id' => $mongo_id,
                            ]);

                            if(isset($iterAff['manager']) && $iterAff['manager']){
                                $dataManager = modelUser::where('name', $iterAff['manager'])->first();
                                if($dataManager){
                                    $data->manager_id = $dataManager->id;
                                }
                            }

                            if(isset($iterAff['firstName']) && isset($iterAff['lastName'])){
                                $data->contact = $iterAff['firstName'] . ' ' . $iterAff['lastName'];
                            }

                            if(isset($iterAff['addr1']) && $iterAff['addr1']){
                                $data->street1 = $iterAff['addr1'];
                            } else if(isset($iterAff['street1'])){
                                $data->street1 = $iterAff['street1'];
                            }
                            if(isset($iterAff['addr2']) && $iterAff['addr2']){
                                $data->street2 = $iterAff['addr2'];
                            } else if(isset($iterAff['street2'])){
                                $data->street2 = $iterAff['street2'];
                            }

                            if(isset($iterAff['country']) && $iterAff['country']){
                                $dataCountry = modelCountry::where('name', $iterAff['country'])->first();
                                if($dataCountry){
                                    $data->country_id = $dataCountry->id;
                                }
                            }
                            if(isset($iterAff['state']) && $iterAff['state']){
                                $dataState = modelState::where('key', $iterAff['state'])->first();
                                if($dataState){
                                    $data->state_id = $dataState->id;
                                }
                            }
                            if(isset($iterAff['city']) && $iterAff['city']){
                                $data->city = $iterAff['city'];
                            }
                            if(isset($iterAff['postalCode']) && $iterAff['postalCode']){
                                $data->zip = $iterAff['postalCode'];
                            } else if(isset($iterAff['zip'])){
                                $data->zip = $iterAff['zip'];
                            }
                            if(isset($iterAff['phone']) && $iterAff['phone']){
                                $data->phone = $iterAff['phone'];
                            }
                            if(isset($iterAff['signupdate']) && $iterAff['signupdate']){
                                $data->created_at = date('Y-m-d H:i:s', $iterAff['signupdate']);
                            }
                            if(isset($iterAff['lastUpdated']) && $iterAff['lastUpdated']){
                                $data->updated_at = $iterAff['lastUpdated'];
                            } else if(isset($iterAff['lastEdited']) && $iterAff['lastEdited']){
                                $data->updated_at = date('Y-m-d H:i:s', $iterAff['lastEdited']);
                            }
                            if(!$data->created_at && $data->updated_at){
                                $data->created_at = $data->updated_at;
                            }
                            if(isset($iterAff['editedBy']) && $iterAff['editedBy']){
                                $dataEditor = modelUser::where('name', $iterAff['editedBy'])->first();
                                if($dataEditor){
                                    $data->updated_by = $dataEditor->name;
                                    $data->updated_by_id = $dataEditor->id;
                                } else {
                                    $data->updated_by = $iterAff['editedBy'];
                                }
                            }
                            if(isset($iterAff['lastLogin']) && $iterAff['lastLogin']){
                                $data->last_login = date('Y-m-d H:i:s', strtotime($iterAff['lastLogin']));
                            }

                            if(isset($iterAff['imnetwork']) && $iterAff['imnetwork']){
                                $data->im_network = $iterAff['imnetwork'];
                            }
                            if(isset($iterAff['imaddresss']) && $iterAff['imaddresss']){
                                $data->im_address = $iterAff['imaddresss'];
                            }

                            $data->save();

                            $result['create'] ++;

                        } catch (Exception $e){
                            var_dump($e->getMessage());
                            var_dump($iterAff);
                            exit();

                        } catch (PDOException $e){
                            var_dump($e->getMessage());
                            var_dump($iterAff);
                            exit();
                        }

                    } else {}
                }
            });

        var_dump($result);
    }

    protected function investigate()
    {
        $result = [];
        $count = 0;

        $mongoDB = DB::connection('mongodb');
        $mongoDB->collection('affiliates')
            ->orderBy('lt_id')
            ->chunk(100, function ($arrAdvertiser) use (&$result, &$count) {

                foreach ($arrAdvertiser as $advert) {

                    foreach($advert as $key => $value){

                        if(empty($result[$key])){
                            $result[$key] = $value;
                        }
                    }

//                    if(empty($advert['email']) &&
//                        empty($advert['country']) &&
//                        empty($advert['city']) &&
//                        empty($advert['street1'])){
//                        var_dump($advert); exit();
//                    }


                    $count ++;
                }

            });

        var_dump('count ' . $count);
        var_dump($result);
    }
}