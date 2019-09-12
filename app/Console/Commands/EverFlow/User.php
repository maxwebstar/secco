<?php

namespace App\Console\Commands\EverFlow;

use Illuminate\Console\Command;

use Exception;
use App\Models\User as modelUser;

class User extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'everflow:user {type}';

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
        $count = 0;

        $path = storage_path();
        $file = $path . "/file/employees.csv";

        if(file_exists($file) == false){
            throw new Exception('Error: File not exist ' . $file);
        }

        $modelUser = new modelUser();

        if (($handle_f = fopen($file, "r")) !== FALSE) {

            while (($data_f = fgetcsv($handle_f, 0, ",")) !== FALSE) {

                if(filter_var($data_f[2], FILTER_VALIDATE_EMAIL) == false){
                    continue;
                }

                $matchID =  preg_match('/[(]([0-9]+)[)]/i', $data_f[0], $arrMatchID);
                if($matchID){

                    $ef_id = $arrMatchID[1];

                    $dataUser = $modelUser->where('email', $data_f[2])->where('ef_id', 0)->first();
                    if($dataUser){
                        $dataUser->ef_id = $ef_id;
                        $dataUser->save();

                        $count ++;
                    }
                }
            }
        }

        var_dump('update: ' . $count);
    }
}
