<?php

namespace App\Console\Commands\Import;

use Illuminate\Console\Command;
use DB;
use Validator;
use PDOException;
use Exception;

use App\Models\IO as modelIO;
use App\Models\User as modelUser;
use App\Models\Country as modelCountry;
use App\Models\Advertiser as modelAdvertiser;
use App\Models\Frequency as modelFrequency;

class IO extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-mongo:io {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
//            case "addition" :
//                $this->addition();
//                break;
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
        $result = [];
        $count = 0;

        $mongoDB = DB::connection('mongodb');
        $mongoDB->collection('activity')
            ->where('type', "newIO")
            //->where('advertiserId', '!=', "")
            ->orderBy('orderNumber')
            ->chunk(100, function ($arrIO) use ($mongoDB, &$result, &$count) {

                foreach($arrIO as $iterIO){

                    $mongo_id = (string) $iterIO['_id'];

                    $exist = modelIO::where('mongo_id', $mongo_id)->first();
                    if(!$exist){

                        $newIO = new modelIO();
                        $newIO->fill([

                            'campaign_name' => $iterIO['campaign_name'],
                            'compCpc' => $iterIO['compCpc'],
                            'compCpa' => $iterIO['compCpa'],
                            'compCpl' => $iterIO['compCpl'],
                            'compCpm' => $iterIO['compCpm'],
                            'compCpd' => $iterIO['compCpd'],
                            'compCpi' => $iterIO['compCpi'],
                            'compCps' => $iterIO['compCps'],

                            'company_name' => $iterIO['company_name'],
                            'company_contact' => $iterIO['company_contact'],
                            'company_phone' => $iterIO['company_phone'],
                            'company_fax' => $iterIO['company_fax'] ? $iterIO['company_fax'] : null,
                            'company_email' => $iterIO['company_email'],
                            'company_street1' => $iterIO['company_address'] ? $iterIO['company_address'] : null,
                            'company_street2' => $iterIO['company_address2'] ? $iterIO['company_address2'] : null,
                            'company_city' => $iterIO['company_city'],
                            'company_state' => $iterIO['company_state'] ? $iterIO['company_state'] : null,
                            'company_zip' => $iterIO['company_zip'] ? $iterIO['company_zip'] : null,

                            'secco_contact' => $iterIO['secco_contact'],
                            'secco_email' => $iterIO['secco_email'],
                            'secco_phone' => $iterIO['secco_phone'] ? $iterIO['secco_phone'] : null,
                            'secco_fax' => $iterIO['secco_fax'] ? $iterIO['secco_phone'] : null,

                            'billing_contact' => $iterIO['billing_contact'],
                            'billing_street1' => $iterIO['billing_address'] ? $iterIO['billing_address'] : null,
                            'billing_street2' => $iterIO['billing_address2'] ? $iterIO['billing_address2'] : null,
                            'billing_city' => $iterIO['billing_city'],
                            'billing_state' => $iterIO['billing_state'] ? $iterIO['billing_state'] : null,
                            'billing_zip' => $iterIO['billing_zip'],

                            'template_document_id' => isset($iterIO['pymntTer']) ? $this->getTemplateDocument($iterIO['pymntTer']) : 0,

                            'traffic_search' => (isset($iterIO['search']) && $iterIO['search'] == 'X') ? 1 : 0,
                            'traffic_banner' => (isset($iterIO['banner']) && $iterIO['banner'] == 'X') ? 1 : 0,
                            'traffic_popup' => (isset($iterIO['popup']) && $iterIO['popup'] == 'X') ? 1 : 0,
                            'traffic_context' => (isset($iterIO['context']) && $iterIO['context'] == 'X') ? 1 : 0,
                            'traffic_exit' => (isset($iterIO['exit']) && $iterIO['exit'] == 'X') ? 1 : 0,
                            'traffic_incent' => (isset($iterIO['incentM']) && $iterIO['incentM'] == 'X') ? 1 : 0,
                            'traffic_path' => (isset($iterIO['path']) && $iterIO['path'] == 'X') ? 1 : 0,
                            'traffic_social' => (isset($iterIO['social']) && $iterIO['social'] == 'X') ? 1 : 0,
                            'traffic_incent_name' => isset($iterIO['incent']) ? $iterIO['incent'] : null,

                            'note' => isset($iterIO['ionotes']) ? $iterIO['ionotes'] : null,
                            'order_number' => $iterIO['orderNumber'],
                            'governing_term' => isset($iterIO['governing']) ? $iterIO['governing'] : null,
                            'mongo_id' => $mongo_id,
                        ]);

                        if($iterIO['advertiserId']){
                            $advertiser = modelAdvertiser::where('lt_id', $iterIO['advertiserId'])->first();
                            if($advertiser){
                                $newIO->advertiser_id = $advertiser->id;
                            }
                        }

                        if(isset($iterIO['cr']) && $iterIO['cr']){
                            switch($iterIO['cr']){
                                case "$ " :
                                    $newIO->currency_id = 1;
                                    break;
                                case "€ " :
                                    $newIO->currency_id = 2;
                                    break;
                                case "£ " :
                                    $newIO->currency_id = 3;
                                    break;
                                default :
                                    break;
                            }
                        }
                        $newIO->company_country = "n/a";
                        $newIO->billing_country = "n/a";
                        if($iterIO['company_country']){
                            $country = modelCountry::where('name', $iterIO['company_country'])->first();
                            if($country){
                                $newIO->company_country = $country->key;
                            }
                        }
                        if($iterIO['billing_country']){
                            $country = modelCountry::where('name', $iterIO['billing_country'])->first();
                            if($country){
                                $newIO->billing_country = $country->key;
                            }
                        }
                        if(isset($iterIO['prePay']) && $iterIO['prePay']){
                            $newIO->prepay = $iterIO['prePay'] == 'yes' ? 1 : 0;
                            if($iterIO['preAmnt']){
                                $newIO->prepay_amount = str_replace([",", "$"], "", $iterIO['preAmnt']);
                            } else {
                                $newIO->prepay_amount = 0;
                            }
                        }
                        if(isset($iterIO['govDate']) && $iterIO['govDate']){
                            $validGovDate = Validator::make([
                                'govDate', $iterIO['govDate']], ['govDate' => 'required|date']);

                            if($iterIO['govDate'] == "new"){
                                $newIO->gov_type = "new";
                                $newIO->gov_date = null;
                            } elseif($iterIO['govDate']){
                                $newIO->gov_type = "date";
                                $newIO->gov_date = date("Y-m-d", strtotime($iterIO['govDate']));
                            }
                        }
                        switch($iterIO['approvalStatus']){
                            case "New IO" :
                                $newIO->status = 1;
                                break;
                            case "approved" :
                                $newIO->status = 3;
                                break;
                            case "Out via Docusign" :
                                $newIO->status = 4;
                                break;
                            case "Duplicate" :
                                $newIO->status = 5;
                                break;
                            default :
                                break;
                        }
                        if(isset($iterIO['creatorId'])){
                            $user = modelUser::where('mongo_user_id', $iterIO['creatorId'])->first();
                            if($user){
                                $newIO->created_by = $user->email;
                                $newIO->created_by_id = $user->id;
                            }
                        }
                        if(isset($iterIO['gdoclink'])) {
                            $newIO->google_url = $iterIO['gdoclink'];

                            $driveIO = $mongoDB->collection('driveIO')->where('link', $iterIO['gdoclink'])->first();
                            if ($driveIO) {

                                $newIO->google_folder = $driveIO['parentId'];
                                $newIO->google_file = $driveIO['fileId'];
                                $newIO->google_file_name = $driveIO['title'];

                                if(isset($driveIO['time'])) {
                                    $newIO->google_created_at = date('Y-m-d H:i:s', strtotime($driveIO['time']));
                                }
                                if(isset($driveIO['type']) && $driveIO['type'] == "governing"){
                                    $newIO->governing = 1;
                                } else {
                                    $newIO->governing = 0;
                                }
                                if(isset($driveIO['govDate']) && $driveIO['govDate']){
                                    $newIO->gov_type = "date";
                                    $newIO->gov_date = date("Y-m-d", strtotime($driveIO['govDate']));
                                }
                            }
                        }
                        if($iterIO['time']){
                            $newIO->time = date('Y-m-d H:i:s', $iterIO['time']);
                        }

                        try {

                            $newIO->save();
                            $count ++;

                        } catch (PDOException $e) {
                            var_dump($e->getMessage());
                            var_dump($iterIO);
                            var_dump($driveIO);
                            exit();
                        }
                    }
                }

            });

        var_dump('count ' . $count);

    }


    public function getTemplateDocument($name)
    {
        $name = trim($name);

        $arr = [
            'Net 15' => 1,
            'Net 30' => 2,
            'Net 45' => 3,
            'custom' => 4,
        ];

        return isset($arr[$name]) ? $arr[$name] : 0;
    }


    protected function investigate()
    {
        $result = [];
        $field = [];
        $count = 0;

        $mongoDB = DB::connection('mongodb');
        $mongoDB->collection('activity')
            ->where('type', 'newIO')
            ->orderBy('orderNumber')
            ->chunk(100, function ($arrIO) use (&$result, &$field, &$count) {

                foreach ($arrIO as $iterIO) {

                    foreach($iterIO as $key => $value){

                        if(empty($result[$key])){
                            $result[$key] = $value;
                        }

                        if($key == 'govDate' && isset($field['govDate'][$value]) == false){
                            $field['govDate'][$value] = 1;
                        }
                        if($key == 'compCpc' && isset($field['compCpc'][$value]) == false){
                            $field['compCpc'][$value] = 1;
                        }
                        if($key == 'compCpa' && isset($field['compCpa'][$value]) == false){
                            $field['compCpa'][$value] = 1;
                        }
                        if($key == 'compCpl' && isset($field['compCpl'][$value]) == false){
                            $field['compCpl'][$value] = 1;
                        }
                        if($key == 'compCpm' && isset($field['compCpm'][$value]) == false){
                            $field['compCpm'][$value] = 1;
                        }
                        if($key == 'compCpd' && isset($field['compCpd'][$value]) == false){
                            $field['compCpd'][$value] = 1;
                        }
                        if($key == 'compCpi' && isset($field['compCpi'][$value]) == false){
                            $field['compCpi'][$value] = 1;
                        }
                        if($key == 'compCps' && isset($field['compCps'][$value]) == false){
                            $field['compCps'][$value] = 1;
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
        var_dump($field);
    }


}
