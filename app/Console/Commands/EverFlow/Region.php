<?php

namespace App\Console\Commands\EverFlow;

use Illuminate\Console\Command;
use Exception;
use App\Services\EverFlow\General as EF_General;
use App\Models\State;

class Region extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'everflow:region {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Investigation\Synchronization of data';

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
            case "get_all" :
                $this->getAll();
                break;

            default :
                throw new Exception('Empty type commaand.');
                break;
        }
    }

    protected function getAll()
    {
        $count = 0;

        $model = new State();
        $EF_General = new EF_General();

        $efRegion = $EF_General->getAllRegion();

        foreach($efRegion as $key => $iter){

            if($iter->country_code == "US") {

                $exist = $model->where('key', $iter->region_code)->first();
                if($exist){
                    $exist->ef_id = $iter->region_id;
                    $exist->save();

                    $count ++;
                }
            }
        }

        var_dump('update ' . $count);
    }
}
