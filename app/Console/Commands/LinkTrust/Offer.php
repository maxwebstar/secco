<?php

namespace App\Console\Commands\LinkTrust;

use Illuminate\Console\Command;

use App\Models\Affiliate as modelOffer;
use App\Models\User as modelUser;

use App\Services\LinkTrust\Offer as LT_Offer;

use PDOException;
use Exception;

class Offer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'linktrust:offer {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronization of offer';

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

        switch ($type) {
            case "sync" :
                $this->sync();
                break;
            default :
                throw new Exception('Empty type commaand.');
                break;
        }
    }


    protected function sync()
    {

    }


}