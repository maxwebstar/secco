<?php

namespace App\Console\Commands\Cron\PipeDrive;

use Illuminate\Console\Command;
use Exception;

use App\Services\PipeDrive\General as PP_General;
use App\Models\User as modelUser;

class User extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron/pipedrive:user {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronization of user';

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
            case "connect" : /* one time for week */
                $this->connect();
                break;
            default :
                throw new Exception('Empty type commaand.');
                break;
        }
    }


    public function connect()
    {
        $result = ['count' => 0];

        $pipeGeneral = new PP_General();

        $pipeUser = $pipeGeneral->getAllUser();

        if($pipeUser){
            foreach($pipeUser as $user){

                $exist = modelUser::where('email',$user['email'])->where('pipedrive_id', 0)->first();
                if($exist){

                    $exist->pipedrive_id = $user['id'];
                    $exist->save();

                    $result['count'] ++;
                }
            }
        }

        var_dump($result);

    }
}
