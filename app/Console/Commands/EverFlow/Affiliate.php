<?php

namespace App\Console\Commands\EverFlow;

use Illuminate\Console\Command;

use App\Models\Affiliate as modelAffiliate;
use App\Models\User as modelUser;
use App\Models\Country as modelCountry;
use App\Models\State as modelState;

use App\Services\EverFlow\Affiliate as EF_Affiliate;

class Affiliate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'everflow:affiliate {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronization of affiliate';

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
            case "sync" :
                $this->sync();
                break;
            default :
                throw new Exception('Empty type commaand.');
                break;
        }
    }


    protected function sync()
    {
        $page = 1;
        $page_size = 100;

        $result = [
            'count' => 0,
            'label' => 0,
            'label_exist' => 0,
            'name' => 0,
            'name_exist' => 0,
            'create' => 0,
            'dublicate' => 0,
            'exist' => 0,
        ];

        $EF_Affiliate = new EF_Affiliate();

        $efResponse =  $EF_Affiliate->getAllAffiliate($page, $page_size);
        if($efResponse->affiliates){

            $this->cycle($efResponse->affiliates, $result);

            $total = $efResponse->paging->total_count;
            if($total > $page_size){
                $page_last = ceil(($total/$page_size));
            } else {
                $page_last = 1;
            }

            $result['count'] = $total;

            while($page_last > $page){
                $page ++;
                $efResponse = $EF_Affiliate->getAllAffiliate($page, $page_size);

                if($efResponse->affiliates){
                    $this->cycle($efResponse->affiliates, $result);
                }
            }
        }

        var_dump($result);
    }


    protected function cycle($efAffiliate, &$result)
    {

        foreach($efAffiliate as $iterAff){

            $exist = modelAffiliate::where('ef_id', $iterAff->network_affiliate_id)->first();
            if(!$exist){

                if($iterAff->relationship->labels && $iterAff->relationship->labels->total){

                    $label = $iterAff->relationship->labels->entries[0];
                    $tmp = explode("_", $label);

                    if(count($tmp) == 3 && is_numeric($tmp[2])){

                        $lt_id = $tmp[2];
                        $search = modelAffiliate::where('lt_id', $lt_id)->first();
                        if($search){

                            if($search->ef_id){
                                $result['label_exist'] ++;
                                continue;
                            }

                            $search->ef_id = $iterAff->network_affiliate_id;
                            $search->ef_status = $iterAff->account_status;
                            $search->save();

                            $result['label'] ++;
                            continue;
                        }
                    }
                }

                $compare = modelAffiliate::where('name', $iterAff->name)->get();
                $count = $compare->count();

                if($count == 1){

                    $data = $compare[0];
                    if($data->ef_id){
                        $result['name_exist'] ++;
                        continue;
                    }

                    $data->ef_id = $iterAff->network_affiliate_id;
                    $data->ef_status = $iterAff->account_status;
                    $data->save();

                    $result['name'] ++;
                    continue;

                } else if($count == 0){

                    $data = new modelAffiliate();
                    $data->fill([
                        'ef_id' => $iterAff->network_affiliate_id,
                        'name' => $iterAff->name,
                        'ef_status' => $iterAff->account_status,
                        'updated_at' => $iterAff->time_saved ? date('Y-m-d H:i:s', $iterAff->time_saved) : date('Y-m-d H:i:s', $iterAff->time_created),
                        'created_at' => date('Y-m-d H:i:s', $iterAff->time_created),
                    ]);

                    $dataManager = modelUser::where('ef_id', $iterAff->network_employee_id)->first();
                    if($dataManager){
                        $data->manager_id = $dataManager->id;

                    } else if(isset($iterAff->relationship->account_manager)){
                        $dataManager = modelUser::where('email', $iterAff->relationship->account_manager->email)->first();
                        if($dataManager){
                            if(!$dataManager->ef_id) {
                                $dataManager->ef_id = $iterAff->network_employee_id;
                                $dataManager->save();
                            }

                            $data->manager_id = $dataManager->id;
                        }
                    }

                    if(isset($iterAff->relationship->contact_address)){

                        $address = $iterAff->relationship->contact_address;

                        $dataCountry = modelCountry::where('ef_id', $address->country_id)->first();
                        if($dataCountry){
                            $data->country_id = $dataCountry->id;
                        }

                        $dataState = modelState::where('key', $address->region_code)->first();
                        if($dataState){
                            $data->state_id = $dataState->id;
                        }

                        $data->city = $address->city ? : null;
                        $data->street1 = $address->address_1 ? : null;
                        $data->street2 = $address->address_2 ? : null;
                        $data->zip = $address->zip_postal_code ? : null;
                    }

                    $data->save();
                    $result['create'] ++;

                } else {
                    $result['dublicate'] ++;
                }
            } else {
                $result['exist'] ++;
            }
        }
    }
}