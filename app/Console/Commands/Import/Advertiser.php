<?php

namespace App\Console\Commands\Import;

use Illuminate\Console\Command;
use DB;
use Validator;
use PDOException;
use Exception;
use App\Models\Advertiser as modelAdvertiser;
use App\Models\User as modelUser;
use App\Models\Country as modelCountry;
use App\Models\State as modelState;
use App\Models\Frequency as modelFrequency;

class Advertiser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-mongo:advertiser {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import advertiser from mongo db to mysql';

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

        switch($type){
            case "import" :
                $this->import();
                break;
            case "addition" :
                $this->addition();
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

        $mongoDB = DB::connection('mongodb');
        $mongoDB->collection('advertisers')
            ->where('name', '>', "")
//            ->where('country', '>', "")
//            ->where('city', '>', "")
//            ->where('street1', '>', "")
//            ->where('currency', '>', "")
            ->orderBy('lt_id')
            ->chunk(100, function ($arrAdvertiser) {

                foreach ($arrAdvertiser as $advert) {

                    $mysqlDB = DB::connection('mysql');

                    $mongo_id = (string) $advert['_id'];

                    $modelAdvertiser = new modelAdvertiser();
                    $exist = $modelAdvertiser->where('lt_id', $advert['lt_id'])
                        ->orWhere('mongo_id', $mongo_id)
                        ->first();

                    if(!$exist){

                        $modelUser = new modelUser();
                        $modelCountry = new modelCountry();
                        $modelState = new modelState();

                        try {

                            $dataInput = [
                                'name' => $advert['name'],
                                'contact' => isset($advert['contact']) ? $advert['contact'] : 'n/a',
                                'email' => isset($advert['email']) ? $advert['email'] : 'n/a (unique '.time().rand (7, 7777).')',
                                'prepay' => 0,
                                'prepay_amount' => 0,
                                'street1' => isset($advert['street1']) ? $advert['street1'] : 'n/a',
                                'street2' => isset($advert['street2']) ? $advert['street2'] : 'n/a',
                                'city' => isset($advert['city']) ? $advert['city'] : 'n/a',
                                'state' => null,
                                'country' => 'n/a',
                                'currency_id' => isset($advert['currency']) ? $this->getCurrency($advert['currency']) : 0,
                                'province' => isset($advert['province']) ? $advert['province'] : null,
                                'zip' => isset($advert['zip']) ? $advert['zip'] : 'n/a',
                                'phone' => isset($advert['phone']) ? $advert['phone'] : 'n/a',
                                'cap' => isset($advert['cap']) ? $advert['cap'] : null,
                                'google_folder' => isset($advert['driveId']) ? $advert['driveId'] : null,
                                'lt_id' => $advert['lt_id'],
                                'created_by' => 'n/a',
                                'mongo_id' => $mongo_id,
                            ];

                            if(isset($advert['prepay'])){
                                if($advert['prepay'] == 'yes'){

                                    $prepayVal = $dataInput['prepay_amount'];
                                    if(strpos($dataInput['prepay_amount'], ",")){
                                        $prepayVal = (int) str_replace(",", "", $dataInput['prepay_amount']);
                                    }

                                    $dataInput['prepay_amount'] = $prepayVal;
                                    $dataInput['prepay'] = 1;
                                }
                            }
                            if(isset($advert['country'])){
                                $dataCountry = $modelCountry->where('key', $advert['country'])
                                    ->orWhere('name', $advert['country'])
                                    ->first();

                                if($dataCountry){
                                    $dataInput['country'] = $dataCountry->key;
                                }
                            }
                            if(isset($advert['state'])){
                                $dataState = $modelState->where('key', $advert['state'])
                                    ->orWhere('name', $advert['state'])
                                    ->first();

                                if($dataState){
                                    $dataInput['state'] = $dataState->key;
                                }
                            }
                            if(isset($advert['qbid']) && $advert['qbid']){
                                $dataInput['quickbook_id'] = $advert['qbid'];
                            }
                            if(isset($advert['managerId'])){
                                $dataManager = $modelUser->where('mongo_user_id', $advert['managerId'])->first();
                                if($dataManager){
                                    $dataInput['manager_id'] = $dataManager->id;
                                }
                            } else {

                            }
                            if(isset($advert['createdBy'])){
                                $dataCreated = $modelUser->where('name', $advert['createdBy'])->first();
                                if($dataCreated){
                                    $dataInput['created_by'] = $dataCreated->email;
                                }
                            }
                            if(isset($advert['createdOn'])){
                                $dataInput['created_at'] = date('Y-m-d H:i:s', $advert['createdOn']);
                            }
                            if(isset($advert['lastUpdated'])){
                                $dataInput['updated_at'] = $advert['lastUpdated'];
                            }
                            if(isset($advert['editedBy'])){
                                $dataEdited = $modelUser->where('name', $advert['editedBy'])->first();
                                if($dataEdited){
                                    $dataInput['edited_by'] = $dataEdited->email;
                                }
                            }
                            if(isset($advert['lastEdited'])){
                                $dataInput['edited_at'] = date('Y-m-d H:i:s', $advert['lastEdited']);
                            }
                            if(isset($advert['frequency']) && $advert['frequency']){

                                $frequencyID = $this->getFrequency($advert['frequency']);
                                $dataInput['frequency_id'] = $frequencyID;

                                if($frequencyID == 4){
                                    $dataInput['frequency_custom'] = $advert['frequency'];
                                }
                            }

                            $newAdvertiser = $modelAdvertiser->create($dataInput);

                        } catch (Exception $e){
                            var_dump($e->getMessage());
                            var_dump($advert);
                            exit();

                        } catch (PDOException $e){
                            var_dump($e->getMessage());
                            var_dump($advert);
                            exit();
                        }
                    } else {

                        $needSave = false;

                        if(!$exist->frequency_id && isset($advert['frequency'])) {

                            $frequencyID = $this->getFrequency($advert['frequency']);
                            $exist->frequency_id = $frequencyID;

                            if($frequencyID == 4){
                                $exist->frequency_custom = $advert['frequency'];
                            }
                            $needSave = true;
                        }
                        if(!$exist->currency_id && isset($advert['currency'])) {
                            $exist->currency_id = $this->getCurrency($advert['currency']);
                            $needSave = true;
                        }
                        if(!$exist->phone && isset($advert['phone'])){
                            $exist->phone = $advert['phone'] ? : 'n/a';
                            $needSave = true;
                        }

                        if($needSave) {
                            $exist->save();
                        }
                    }
                }

            });
    }


    public function getFrequency($name)
    {
        $arr = [
            'monthly' => 1,
            'oneTime' => 4,
        ];

        return isset($arr[$name]) ? $arr[$name] : 0;
    }


    public function getCurrency($name)
    {
        $arr = [
            'USD' => 1,
            'EUR' => 2,
            'GBP' => 3,
        ];

        return isset($arr[$name]) ? $arr[$name] : 0;
    }


    protected function addition()
    {
        $mysqlDB = DB::connection('mysql');

        $mongoDB = DB::connection('mongodb');
        $mongoDB->collection('advertisers')
            ->orderBy('lt_id')
            ->chunk(100, function ($arrAdvertiser) use ($mysqlDB) {

                foreach ($arrAdvertiser as $advert) {

                    if(!empty($advert['time']) ||
                        !empty($advert['statReqUser']) ||
                        !empty($advert['edateTime']) ||
                        !empty($advert['frequency']) ||
                        !empty($advert['lastSent']) ||
                        !empty($advert['statRequest']) ||
                        !empty($advert['nextRecurring']) ||
                        !empty($advert['sent']) ||
                        !empty($advert['recurring'])){

                        if(isset($advert['time']) && $advert['time'] == "immed"){ continue; }

                        $sql = "
                            INSERT INTO `import_addition_advertiser` (
                                `lt_id`, 
                                `mongo_id`, 
                                `time`, 
                                `statReqUser`,	
                                `edateTime`, 
                                `frequency`, 
                                `lastSent`, 
                                `statRequest`,
                                `nextRecurring`, 
                                `sent`, 
                                `recurring`,
                                `created_at`
                            ) VALUES (
                                :lt_id,	
                                :mongo_id,	
                                :time_val,	
                                :statReqUser,
                                :edateTime,
                                :frequency,	
                                :lastSent, 
                                :statRequest, 
                                :nextRecurring, 
                                :sent,	
                                :recurring,
                                :created_at
                            ) ON DUPLICATE KEY UPDATE `updated_at` = :updated_at";

                        $dataInput = [
                            'lt_id' => $advert['lt_id'],
                            'mongo_id' => (string) $advert['_id'],
                            'time_val' => isset($advert['time']) ? date('Y-m-d H:i:s', $advert['time']) : null,
                            'statReqUser' => isset($advert['statReqUser']) ? $advert['statReqUser'] : null,
                            'edateTime' => isset($advert['edateTime']) ? date('Y-m-d H:i:s', $advert['edateTime']) : null,
                            'frequency' => isset($advert['frequency']) ? $advert['frequency'] : null,
                            'lastSent' => isset($advert['lastSent']) ? date('Y-m-d H:i:s', $advert['lastSent']) : null,
                            'statRequest' => isset($advert['statRequest']) ? $advert['statRequest'] : null,
                            'nextRecurring' => isset($advert['nextRecurring']) ? date('Y-m-d H:i:s', $advert['nextRecurring']) : null,
                            'sent' => isset($advert['sent']) ? $advert['sent'] : null,
                            'recurring' => isset($advert['recurring']) ? $advert['recurring'] : null,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'created_at' => date('Y-m-d H:i:s'),
                        ];

                        try{

                            $mysqlDB->statement($sql, $dataInput);

                        } catch (PDOException $e){

                            var_dump($e->getMessage());
                            var_dump($advert);
                            exit();
                        }
                    }
                }

            });
    }


    protected function investigate()
    {
        $result = [];
        $count = 0;

        $mongoDB = DB::connection('mongodb');
        $mongoDB->collection('advertisers')
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
