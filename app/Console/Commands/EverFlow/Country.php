<?php

namespace App\Console\Commands\EverFlow;

use Illuminate\Console\Command;

use App\Models\Country as modelCountry;
use App\Services\EverFlow\General as EF_General;

class Country extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'everflow:country {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronization of data';

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
            case "sync_id" :
                $this->syncID();
                break;

            default :
                throw new Exception('Empty type commaand.');
                break;
        }
    }


    protected function syncID()
    {
        $EF_General = new EF_General();
        $modelCountry = new modelCountry();

        $efCountry = $EF_General->getAllCountry();

        foreach($efCountry as $key => $iter){

            $data = $modelCountry->where('key', $iter->country_code)
                ->where('ef_id', 0)
                ->first();

            if($data){
                $data->ef_id = $iter->country_id;
                $data->save();
            }
        }
    }



}
