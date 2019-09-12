<?php

namespace App\Console\Commands\EverFlow;

use Illuminate\Console\Command;

use App\Models\Advertiser as modelAdvertiser;
use App\Services\EverFlow\Advertiser as EF_Advertiser;

class Advertiser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'everflow:advertiser {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronization of advertisers';

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
            case "connect" :
                $this->connect();
                break;


            default :
                throw new Exception('Empty type commaand.');
                break;
        }
    }


    protected function connect()
    {
        $page = 1;
        $page_size = 100;
        $count = ['all' => 0, 'not_found' => 0, 'not_found_compare' => 0];
        $dublicate = [];

        $EF_Advertiser = new EF_Advertiser();

        $efResponse =  $EF_Advertiser->getAllAdvertiser($page, $page_size);

        if($efResponse->advertisers){

            $this->connectCycle($efResponse->advertisers, $count, $dublicate);

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
                    $this->connectCycle($efResponse->advertisers, $count, $dublicate);
                }
            }
        }

        var_dump($dublicate);
        var_dump($count);
    }


    protected function connectCycle($efAdvertiser, &$count, &$dublicate)
    {
        $modelAdvertiser = new modelAdvertiser();

        foreach($efAdvertiser as $advert){

            $existAll = $modelAdvertiser->where('name', $advert->name)->get();
            $countExist = $existAll->count();

            if($countExist == 1){

                $exist = $existAll[0];
                if($exist->ef_id){
                    continue;
                }

                $exist->ef_id = $advert->network_advertiser_id;
                $exist->ef_status = $advert->account_status;
                $exist->save();

            } else if($countExist > 1) {

                $compare = $modelAdvertiser->where('name', $advert->name)
                    ->whereHas('manager', function($query) use ($advert){
                        $query->where('ef_id', $advert->network_employee_id);
                    })->get();

                $countCompare = $compare->count();
                if($countCompare == 1){

                    $exist = $compare[0];
                    if($exist->ef_id){
                        continue;
                    }

                    $exist->ef_id = $advert->network_advertiser_id;
                    $exist->ef_status = $advert->account_status;
                    $exist->save();

                } elseif($countCompare > 1) {
                    var_dump($advert->name);
                    $dublicate[$advert->name][$advert->network_employee_id][] = $advert->network_advertiser_id;
                } else {
                    $dublicate[$advert->name][$advert->network_employee_id][] = $advert->network_advertiser_id;
                    $count['not_found_compare'] ++;
                }

            } else if($countExist == 0) {
                $count['not_found'] ++;
            }

            $count['all'] ++;
        }
    }
}
