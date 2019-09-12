<?php

namespace App\Console\Commands\LinkTrust;

use Illuminate\Console\Command;

use App\Models\Affiliate as modelAffiliate;
use App\Models\User as modelUser;
use App\Models\State as modelState;
use App\Models\Country as modelCountry;
use App\Services\LinkTrust\Affiliate as LT_Affiliate;

use PDOException;
use Exception;

class Affiliate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'linktrust:affiliate {type}';

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

        switch ($type) {
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
        $result = [
            'count' => 0,
            'create' => 0,
            'update' => 0,
            'dublicate' => 0,
            'exist' => 0,
            'exist_by_name' => 0
        ];

        $LT_Affiliate = new LT_Affiliate();

        $ltAffiliate =  $LT_Affiliate->getAllAffiliate();
        if($ltAffiliate){

            $result['count'] = count($ltAffiliate);

            foreach($ltAffiliate as $iterAff){

                $exist = modelAffiliate::where('lt_id', $iterAff->AffiliateId)->first();
                if(!$exist){

                    $compare = modelAffiliate::where('name', $iterAff->AffiliateName)->get();
                    $count = $compare->count();

                    if($count == 1){

                        if($compare[0]->lt_id){
                            $result['exist_by_name'] ++;
                            continue;
                        } else {

                            $data = $compare[0];
                            $data->fill([
                                'lt_id' => $iterAff->AffiliateId,
                            ]);
                            $data->save();

                            $result['update'] ++;
                        }

                    } else if($count == 0){

                        try {

                            $data = new modelAffiliate();
                            $data->fill([
                                'lt_id' => $iterAff->AffiliateId,
                                'name' => $iterAff->AffiliateName,
                                'email' => $iterAff->Email ?: null,
                                'city' => $iterAff->City ?: null,
                                'street1' => $iterAff->Address1 ?: null,
                                'street2' => $iterAff->Address2 ?: null,
                                'phone' => $iterAff->Phone ?: null,
                                'zip' => $iterAff->PostalCode ?: null,
                                'im_network' => $iterAff->IMNetwork ?: null,
                                'im_address' => $iterAff->IMAddress ?: null,
                            ]);

                            if ($iterAff->PrimaryManager) {
                                $dataManager = modelUser::where('name', $iterAff->PrimaryManager)->first();
                                if ($dataManager) {
                                    $data->manager_id = $dataManager->id;
                                }
                            }

                            if ($iterAff->FirstName && $iterAff->LastName) {
                                $data->contact = $iterAff->FirstName . ' ' . $iterAff->LastName;
                            }

                            if ($iterAff->Country) {
                                $dataCountry = modelCountry::where('name', $iterAff->Country)->first();
                                if ($dataCountry) {
                                    $data->country_id = $dataCountry->id;
                                }
                            }
                            if ($iterAff->State) {
                                $dataState = modelState::where('key', $iterAff->State)->first();
                                if ($dataState) {
                                    $data->state_id = $dataState->id;
                                }
                            }

                            if ($iterAff->SignupDate) {
                                $data->updated_at = date('Y-m-d H:i:s', strtotime($iterAff->SignupDate));
                                $data->created_at = date('Y-m-d H:i:s', strtotime($iterAff->SignupDate));
                            }
                            if ($iterAff->LastLoginDate) {
                                $data->last_login = date('Y-m-d H:i:s', strtotime($iterAff->LastLoginDate));
                            }

                            $data->save();

                            $result['create']++;

                        } catch (PDOException $e) {
                            var_dump($e->getMessage());
                            dd($iterAff);
                        } catch (Exception $e) {
                            var_dump($e->getMessage());
                            dd($iterAff);
                        }

                    } else {
                        $result['dublicate'] ++;
                    }
                } else {
                    $result['exist'] ++;
                }
            }
        }

        var_dump($result);
    }

}