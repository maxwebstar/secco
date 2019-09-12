<?php
namespace App\Console\Commands\Cron;

use Illuminate\Console\Command;

use Validator;
use DB;

use DateTime;
use DateTimeZone;
use Exception;
use PDOException;


class TestMy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron/test:my {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Only for test';


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
            case "time" :
                $this->time();
                break;
            default :
                throw new Exception('Empty type commaand.');
                break;
        }
    }


    protected function time()
    {
        var_dump(date("Y-m-d H:i:s"));
    }

}