<?php

namespace App\Console\Commands\Cron\EverFlow;

use Illuminate\Console\Command;

use App\Models\Request\Cap as modelRequestCap;
use App\Services\EverFlow\Offer as EF_Offer;


class Cap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron/everflow:cap {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run cap request';

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
            case "request" :
                $this->request();
                break;
            default :
                throw new Exception('Empty type commaand.');
                break;
        }
    }


    protected function request()
    {
        $count = 0;
        $ef_Offer = new EF_Offer();

        $dataRequest = modelRequestCap::where('date', '<=', date('Y-m-d'))
            ->whereNull('error_cron')
            ->where('status', 4)
            ->get();

        if($dataRequest){
            foreach($dataRequest as $iter){

                $dataOffer = $iter->offer;
                if($dataOffer->ef_id){

                    $ef_resp = $ef_Offer->updateOfferCap($dataOffer, $iter);
                    if($ef_resp['updated']){
                        $iter->status = 3;
                    } else {
                        $iter->error_cron = $ef_resp['message'];
                    }

                    $iter->save();
                    $count ++;
                }
            }
        }

        var_dump('result ' . $count);
    }

}