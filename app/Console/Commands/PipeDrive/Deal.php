<?php

namespace App\Console\Commands\PipeDrive;

use Illuminate\Console\Command;
use PDOException;
use Exception;

use App\Services\PipeDrive\Organization as PP_Organization;
use App\Services\PipeDrive\Person as PP_Person;

use App\Models\PipeDrive\Deal as modelDeal;
use App\Models\Currency as modelCurrency;
use App\Models\Country as modelCountry;
use App\Models\User as modelUser;

class Deal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pipedrive:deal {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronization of deals';

    protected $result;

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

        switch($type) {
            case "update" :
                $this->update();
                break;
            default :
                throw new Exception('Empty type command.');
                break;
        }
    }


    public function update()
    {
        $dataExist = modelDeal::whereIn('id', [1, 2, 3, 4, 5, 6])->get();

        foreach($dataExist as $iterDeal) {

            $param = json_decode($iterDeal->request_body);

            if ($param->current->status != "won") {
                var_dump(['success' => 'Deal not won']);
                continue;
            }

            $pipePerson = new PP_Person();
            $pipeOrganization = new PP_Organization();

            $data = modelDeal::where('pd_deal_id', $param->current->id)->first();
            if ($data) {
                if ($data->status == 0) {
                    var_dump(['success' => 'Deal is deleted']);
                    continue;
                } else if($data->status == 3) {
                    var_dump(['success' => 'Deal is already added']);
                    continue;
                }
            } else {
                $data = new modelDeal();
            }

            $data->pd_deal_id = $param->current->id;
            $data->io_campaign_name = $param->current->title;
            //$data->status = 1;

            if ($param->current->currency) {
                $currency = modelCurrency::where('key', $param->current->currency)->first();
                $data->currency_id = $currency ? $currency->id : 0;
            }
            if ($param->current->user_id) {
                $data->pd_user_id = $param->current->user_id;
                $user = modelUser::where('pipedrive_id', $param->current->user_id)->first();
                $data->manager_id = $user ? $user->id : 0;
            }

            if ($param->current->org_id) {
                $data->pd_organization_id = $param->current->org_id;
                $dataOrg = $pipeOrganization->getByID($param->current->org_id);
                if ($dataOrg) {
                    $data->advertiser_name = $dataOrg['data']['name'];
                    $data->advertiser_street1 = $dataOrg['data']['address_formatted_address'];
                    $data->advertiser_zip = $dataOrg['data']['address_postal_code'];

                    if ($dataOrg['data']['address_country']) {
                        $country = modelCountry::where('name', $dataOrg['data']['address_country'])->first();
                        $data->advertiser_country = $country ? $country->key : null;
                    }
                }
            }
            if ($param->current->person_id) {
                $data->pd_person_id = $param->current->person_id;
                $dataPerson = $pipePerson->getByID($param->current->person_id);
                if ($dataPerson) {
                    $data->advertiser_contact = $dataPerson['data']['name'];

                    if (isset($dataPerson['data']['email']) && is_array($dataPerson['data']['email'])) {
                        $valueEmail = "";
                        foreach ($dataPerson['data']['email'] as $iter) {
                            $valueEmail .= $iter['value'] . ", ";
                        }
                        $data->advertiser_email = $valueEmail ? substr($valueEmail, 0, -2) : null;
                    }
                    if (isset($dataPerson['data']['phone']) && is_array($dataPerson['data']['phone'])) {
                        $valuePhone = "";
                        foreach ($dataPerson['data']['phone'] as $iter) {
                            $valuePhone .= $iter['value'] . ", ";
                        }
                        $data->advertiser_phone = $valuePhone ? substr($valuePhone, 0, -2) : null;
                    }
                }
            }

            try {

                $data->save();

            } catch (PDOException $e) {
                var_dump(['error' => $e->getMessage()]);
                continue;
            } catch (Exception $e) {
                var_dump(['error' => $e->getMessage()]);
                continue;
            }

            var_dump(['success' => 'ok']);
        }
    }

}