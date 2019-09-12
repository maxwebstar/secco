<?php

namespace App\Console\Commands\Cron\EverFlow;

use Illuminate\Console\Command;

use App\Models\AdvertiserStat as modelAdvertiserStat;
use App\Models\Advertiser as modelAdvertiser;
use App\Models\AdvertiserMissing as modelAdvertiserMissing;
use App\Models\User as modelUser;
use App\Models\Currency as modelCurrency;
use App\Models\Country as modelCountry;
use App\Models\State as modelState;
use App\Services\EverFlow\Advertiser as EF_Advertiser;

use DB;
use DateTime;
use DateTimeZone;
use PDOException;
use Exception;

class Advertiser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron/everflow:advertiser {type} {param1?} {param2?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronization of advertisers';

    protected $result;
    protected $allow_create;
    protected $need_create;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->result = [
            'sync' => 0,
            'create' => 0,
            'create_missing' => 0,
            'update_missing' => 0,
            'add_id' => 0,
            'inactive' => 0
        ];

        $this->allow_create = [
            128220, 128205, 128204, 128201, 128200, 128199, 128198, 128196,
            128192, 128191, 128180, 128175, 128174, 128173, 128172, 128171,
            128170, 128169, 128167, 128166, 128165, 128164, 128162, 128161,
            128160, 123, 161, 133, 87, 157
        ];

        $this->need_create = [
            128215, 128214, 128213, 128211, 128207, 128203, 128202, 128168,
            128188, 128181, 128163, 128155, 128154, 123, 161, 87
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

        switch($type){
            case "sync" :
                $this->sync();
                break;
            case "sync-statistic-yesterday" :
                $this->syncStatisticYesterday();
                break;
            case "sync-statistic-manual" :
                $this->syncStatisticManual();
                break;
            case "create-folder" :
                $this->createFolder();
                break;
            case "remove-missing-duplicate" :
                $this->removeMissingDuplicate();
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

        $EF_Advertiser = new EF_Advertiser();

        $efResponse = $EF_Advertiser->getAllAdvertiser($page, $page_size);

        if($efResponse->advertisers){

            $this->syncCycle($efResponse->advertisers);

            $total = $efResponse->paging->total_count;
            if($total > $page_size){
                $page_last = ceil(($total/$page_size));
            } else {
                $page_last = 1;
            }

            while($page_last > $page){
                $page ++;
                $efResponse = $EF_Advertiser->getAllAdvertiser($page, $page_size);

                if($efResponse->advertisers){
                    $this->syncCycle($efResponse->advertisers);
                }
            }
        }

        var_dump($this->result);
    }


    protected function syncCycle($efAdvertiser)
    {
        foreach($efAdvertiser as $advert){

            $exist = modelAdvertiser::where('ef_id', $advert->network_advertiser_id)->first();
            if($exist){

                $this->asyncAdvertiser($advert, $exist);

            } else {

                $this->searchAdvertiser($advert);
            }
        }
    }


    protected function searchAdvertiser($advert)
    {
        $advertSearch = modelAdvertiser::where('name', $advert->name)
            ->where('lt_id', $advert->network_advertiser_id)
            ->where('ef_id', 0)
            ->get();

        if($advertSearch->count() == 1){

            $advertSearch[0]->ef_id = $advert->network_advertiser_id;
            $advertSearch[0]->save();

            $this->result['add_id'] ++;

        } else if($advert->account_status == "active" || $advert->account_status == "pending") {

            $advertSearch = modelAdvertiser::where('name', $advert->name)
                ->orWhere('lt_id', $advert->network_advertiser_id)
                ->get();

            if($advertSearch->count() == 0){

                /*if(in_array($advert->network_advertiser_id, $this->allow_create)){
                    $this->createAdvertiser($advert);
                }*/

                $this->createAdvertiserMissing($advert);

            } else {

                /*if(in_array($advert->network_advertiser_id, $this->need_create)){
                    $this->createAdvertiser($advert);
                }*/
                $this->createAdvertiserMissing($advert, 1);
            }

        } else {
            $this->result['inactive'] ++;
        }
    }


    protected function createAdvertiser($advert)
    {
        exit();

        $data = new modelAdvertiser();
        $data->fill([
            'ef_id' => $advert->network_advertiser_id,
            'ef_status' => $advert->account_status,
            'name' => $advert->name,
            'contact' => 'n/a',
            'email' => 'n/a',
            'created_by' => "import from ef",
            'created_by_id' => 0,
        ]);

        if($advert->default_currency_id){
            $currency = modelCurrency::where('key', $advert->default_currency_id)->first();
            if($currency){
                $data->currency_id = $currency->id;
            }
        }
        if($advert->sales_manager_id) {
            $dataUser = modelUser::where('ef_id', $advert->sales_manager_id)->first();
            if ($dataUser) {
                $data->manager_id = $dataUser->id;
            }
        }
        if($advert->network_employee_id) {
            $dataUser = modelUser::where('ef_id', $advert->network_employee_id)->first();
            if ($dataUser) {
                $data->manager_account_id = $dataUser->id;
            }
        }
        if(isset($advert->relationship->contact_address)){
            $contact_address = $advert->relationship->contact_address;
            if($contact_address->address_1){
                $data->street1 = $contact_address->address_1;
            }
            if($contact_address->region_code){
                $state = modelState::where('key', $contact_address->region_code)->first();
                if($state){
                    $data->state = $state->key;
                }
            }
            if($contact_address->city){
                $data->city = $contact_address->city;
            }
            if($contact_address->country_id){
                $country = modelCountry::where('ef_id', $contact_address->country_id)->first();
                if($country){
                    $data->country = $country->key;
                }
            }
            if($contact_address->zip_postal_code){
                $data->zip = $contact_address->zip_postal_code;
            }
        }

        if(!$data->country){
            $data->country = "n/a";
        }
        if(!$data->city){
            $data->city = "n/a";
        }

        try {

            $data->save();

        } catch (PDOException $e) {

            var_dump($e->getMessage());
            var_dump($data);
            exit();
        }

        var_dump("ef_id " . $data->ef_id . " name " . $data->name);

        $this->result['create'] ++;
    }


    protected function createAdvertiserMissing($advert, $is_duplicate = 0)
    {
        $data = modelAdvertiserMissing::where('ef_id', $advert->network_advertiser_id)->first();
        if(!$data){

            $data = new modelAdvertiserMissing();
            $data->ef_id = $advert->network_advertiser_id;
            $data->ef_status = $advert->account_status;
            $data->email = 'n/a';
            $data->name = 'n/a';
            $data->is_duplicate = $is_duplicate;
            $data->status = 1;

            if($advert->default_currency_id){
                $currency = modelCurrency::where('key', $advert->default_currency_id)->first();
                if($currency){
                    $data->currency_id = $currency->id;
                }
            }
            if($advert->sales_manager_id) {
                $dataUser = modelUser::where('ef_id', $advert->sales_manager_id)->first();
                if ($dataUser) {
                    $data->manager_id = $dataUser->id;
                }
            }
            if($advert->network_employee_id) {
                $dataUser = modelUser::where('ef_id', $advert->network_employee_id)->first();
                if ($dataUser) {
                    $data->manager_account_id = $dataUser->id;
                }
            }
            if(isset($advert->relationship->contact_address)){
                $contact_address = $advert->relationship->contact_address;
                if($contact_address->address_1){
                    $data->street1 = $contact_address->address_1;
                }
                if($contact_address->region_code){
                    $state = modelState::where('key', $contact_address->region_code)->first();
                    if($state){
                        $data->state = $state->key;
                    }
                }
                if($contact_address->city){
                    $data->city = $contact_address->city;
                }
                if($contact_address->country_id){
                    $country = modelCountry::where('ef_id', $contact_address->country_id)->first();
                    if($country){
                        $data->country = $country->key;
                    }
                }
                if($contact_address->zip_postal_code){
                    $data->zip = $contact_address->zip_postal_code;
                }
            }

            if(!$data->country){
                $data->country = "n/a";
            }
            if(!$data->city){
                $data->city = "n/a";
            }

            try {

                $data->save();

            } catch (PDOException $e) {

                var_dump($e->getMessage());
                var_dump($data);
                exit();
            }

            var_dump("ef_id " . $data->ef_id . " name " . $data->name);

            $this->result['create_missing'] ++;

        } else {

            $data->ef_status = $advert->account_status;
            $data->save();

            $this->result['update_missing'] ++;
        }
    }


    protected function removeMissingDuplicate()
    {
        var_dump("script disabled"); exit();

        $sqlDB = DB::connection('mysql');
        $sqlDB->table('advertiser_missing')
            ->select('ef_id')
            ->where('status', 2)
            ->groupBy('ef_id')
            ->orderBy('ef_id')
            ->chunk(100, function ($arr) {

                foreach($arr as $iter){

//                    $data = modelAdvertiserMissing::where('status', 2)
//                        ->where('ef_id', $iter->ef_id)
//                        ->orderBy('id', 'DESC')
//                        ->get();
//
//                    $count = $data->count();
//                    if($count > 1){
//
//                        var_dump("ef_id " . $iter->ef_id);
//                        var_dump("count " . $count);
//
//                        while($count > 1){
//
//                            $key = $count - 1;
//
//                            if(isset($data[$key]) && isset($data[($count - 2)])){
//                                var_dump("iter " . $key);
//                                $data[$key]->delete();
//                            } else {
//                                var_dump("iter not found " . $key);
//                            }
//
//                            $count --;
//                        }
//                    }


//                    $data = modelAdvertiserMissing::whereIn('status', [1])
//                        ->where('ef_id', $iter->ef_id)
//                        ->orderBy('id', 'DESC')
//                        ->get();
//
//                    if($data){
//                        foreach($data as $iter){
//                            $iter->delete();
//                        }
//                    }

                }

            });
    }


    protected function asyncAdvertiser($advert, $exist)
    {
        if($advert->sales_manager_id) {
            $dataUser = modelUser::where('ef_id', $advert->sales_manager_id)->first();
            if ($dataUser) {
                $exist->manager_id = $dataUser->id;
            }
        }
        if($advert->network_employee_id) {
            $dataUser = modelUser::where('ef_id', $advert->network_employee_id)->first();
            if ($dataUser) {
                $exist->manager_account_id = $dataUser->id;
            }
        }

        $exist->name = $advert->name;
        $exist->ef_status = $advert->account_status;
        $exist->save();

        $this->result['sync'] ++;
    }


    protected function syncStatisticYesterday()
    {
        $count = [
            'revenue' => 0,
            'lost_revenue' => 0,
            'lost_count' => 0,
            'lost_advertiser' => [],
        ];

        $dateStart = new DateTime('NOW', new DateTimeZone("America/New_York"));
        $dateEnd = new DateTime('NOW', new DateTimeZone("America/New_York"));

        $dateStart->modify('-1 day');

        $EF_Advertiser = new EF_Advertiser();

        $efStat = $EF_Advertiser->getStat($dateStart->format('Y-m-d'), $dateEnd->format('Y-m-d'));

        if($efStat){
            $this->loadStatistic($efStat, $count);
        }

        var_dump($count);
    }


    protected function syncStatisticManual()
    {
        $count = [
            'revenue' => 0,
            'lost_revenue' => 0,
            'lost_count' => 0,
            'lost_advertiser' => [],
        ];

        $dateStart = $this->argument('param1');
        $dateEnd = $this->argument('param2');

        if(DateTime::createFromFormat('Y-m-d', $dateStart) === FALSE){
            throw new Exception("Invalid date start from, correct form = 'YYYY-mm-dd'");
        }
        if(DateTime::createFromFormat('Y-m-d', $dateEnd) === FALSE){
            throw new Exception("Invalid date end from, correct form = 'YYYY-mm-dd'");
        }

        if(strtotime($dateEnd) <= strtotime($dateStart)){
            throw new Exception("Invalid range for date start and date end");
        }

        $dateStart = new DateTime($dateStart, new DateTimeZone("America/New_York"));
        $dateEnd = new DateTime($dateEnd, new DateTimeZone("America/New_York"));

        while($dateStart <= $dateEnd){

            $date = $dateStart->format("Y-m-d");
            var_dump($date);

            $EF_Advertiser = new EF_Advertiser();

            $efStat = $EF_Advertiser->getStat($date, $date);
            if($efStat){
                $this->loadStatistic($efStat, $count);
            }

            $dateStart->modify('+1 day');

        }

        var_dump($count);
    }


    protected function loadStatistic($efStat, &$count)
    {
        foreach($efStat->table as $iter){

            if($iter->columns[0]->column_type == "advertiser"){
                $ef_id = $iter->columns[0]->id;
            }
            if($iter->columns[1]->column_type == "date"){
                $date = $iter->columns[1]->id;
            }

            $dataAdvertiser = modelAdvertiser::where('ef_id', $ef_id)->first();
            if($dataAdvertiser){

                $dataStat = modelAdvertiserStat::where('date', date("Y-m-d", $date))
                    ->where('network_id', 2)
                    ->where('advertiser_id', $dataAdvertiser->id)
                    ->where('ef_id', $ef_id)
                    ->first();

                if($dataStat){
                    $dataStat->click = $iter->reporting->total_click;
                    $dataStat->revenue = $iter->reporting->revenue;
                    $dataStat->payout = $iter->reporting->payout;
                    $dataStat->profit = $iter->reporting->profit;
                    $dataStat->save();
                } else {
                    modelAdvertiserStat::create([
                        'advertiser_id' => $dataAdvertiser->id,
                        'ef_id' => $ef_id,
                        'network_id' => 2,
                        'date' => date("Y-m-d", $date),
                        'approved' => $iter->reporting->cv,
                        'click' => $iter->reporting->total_click,
                        'revenue' => $iter->reporting->revenue,
                        'payout' => $iter->reporting->payout,
                        'profit' => $iter->reporting->profit
                    ]);
                }

                $count['revenue'] += $iter->reporting->revenue;
            } else {
                $count['lost_revenue'] += $iter->reporting->revenue;
                //$count['lost_advertiser'][] = $ef_id;
                $count['lost_count'] ++;
            }
        }
    }


    protected function createFolder()
    {
        $data = modelAdvertiser::where('id', 1868)->first();
        $data->createGoogleDriveFolder();
        $data->save();
    }
}
