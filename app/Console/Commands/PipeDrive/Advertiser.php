<?php

namespace App\Console\Commands\PipeDrive;

use Illuminate\Console\Command;
use Exception;

use App\Services\PipeDrive\Organization as PP_Organization;
use App\Models\Advertiser as modelAdvertiser;

class Advertiser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pipedrive:advertiser {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronization of advertisers';

    protected $result;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->result = [
            'connect' => [
                'count' => 0,
                'dublicate' => 0,
                'not_found' => 0,
                'exist' => 0,
            ]
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

        switch($type) {
            case "connect" : /* one time for ever */
                $this->connect(0, 0);
                var_dump($this->result['connect']);
                break;
            default :
                throw new Exception('Empty type command.');
                break;
        }
    }


    public function connect($start, $limit)
    {
        var_dump("start " . $start);

        $pipeOrg = new PP_Organization();
        $dataOrg = $pipeOrg->getAll($start, $limit);

        if($dataOrg){

            $this->connectCycle($dataOrg['data']);

            if(isset($dataOrg['additional_data']) &&
               isset($dataOrg['additional_data']['pagination']) &&
               isset($dataOrg['additional_data']['pagination']['next_start'])){

                $start = $dataOrg['additional_data']['pagination']['next_start'];
                $limit = $dataOrg['additional_data']['pagination']['limit'];

                $this->connect($start, $limit);
            }
        }
    }


    public function connectCycle($dataOrg)
    {
        foreach($dataOrg as $org){

            $exist = modelAdvertiser::where('pipedrive_id', $org['id'])->first();
            if(!$exist){

                $advertAll = modelAdvertiser::where('name', $org['name'])->where('pipedrive_id', 0)->get();
                $advertCount = $advertAll->count();

                if($advertCount == 1){

                    $advert = $advertAll[0];
                    $advert->pipedrive_id = $org['id'];
//                    $advert->save();

                    $this->result['connect']['count'] ++;

                } else if ($advertCount > 1) {
                    $this->result['connect']['dublicate'] ++;
                } else {
                    $this->result['connect']['not_found'] ++;
                }

            } else {
                $this->result['connect']['exist'] ++;
            }
        }
    }



}