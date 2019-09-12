<?php
namespace App\Console\Commands\Cron\QB;

use Illuminate\Console\Command;

use App\Services\Mailer as Mailer;

use App\Models\User as modelUser;
use App\Models\AdvertiserStat as modelAdvertiserStat;

use App\Models\Advertiser as modelAdvertiser;
use App\Models\CreditCap as modelCreditCap;

use App\Models\QB\Report as modelQBReport;
use App\Models\QB\Report\Mounth as modelQBReportMonth;
use App\Models\QB\Customer as modelCustomer;

use Validator;
use DB;

use DateTime;
use DateTimeZone;
use Exception;
use PDOException;
use PHPMailer\PHPMailer\Exception AS ExceptionPHPMailer;


class CreditCap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron/qb:credit-cap {type} {param1?} {param2?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate credit cap data';

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
            case "test" :
                $this->test();
                break;
            case "month" :
                $this->month();
                break;
            case "month-current" :
                $this->monthCurrent();
                break;
            case "generate" :
                $this->generate();
                break;
            case "notify-limit" :
                $this->notifyLimit();
                break;
            default :
                throw new Exception('Empty type commaand.');
                break;
        }
    }


    protected function test()
    {
        $result = [
            'count' => 0,
        ];

        $sqlDB = DB::connection('mysql');

        modelAdvertiser::orderBy('id')
            ->chunk(50, function ($dataAdvertiser) use (&$result, &$sqlDB) {

                foreach ($dataAdvertiser as $advertiser) {

                    $data = modelCreditCap::firstOrCreate(['advertiser_id' => $advertiser->id]);

                    $dataStat = $sqlDB->table('advertiser_stat')
                        ->select(DB::raw('SUM(approved) as approved'), DB::raw('SUM(click) as click'), DB::raw('SUM(revenue) as	revenue'), DB::raw('SUM(payout) as payout'), DB::raw('SUM(profit) as profit'))
                        ->where('advertiser_id', $advertiser->id)
                        ->first();

                    if($dataStat){
                        $data->fill([
                            'advertiser_id' => $advertiser->id,
                            'revenue' => $dataStat->revenue ? : 0,
                            'balance' => 0,
                            'cap' => $advertiser->cap,
                        ]);
                    } else {
                        $data->fill([
                            'advertiser_id' => $advertiser->id ? : 0,
                            'revenue' => 0,
                            'balance' => 0,
                            'cap' => $advertiser->cap,
                        ]);
                    }

                    $data->save();

                    $result['count'] ++;
                }
            });

        var_dump($result);
    }


    protected function month()
    {
        $param1 = $this->argument('param1');
        $param2 = $this->argument('param2');

        if(DateTime::createFromFormat('Y-m-02', $param1) !== FALSE &&
           DateTime::createFromFormat('Y-m-01', $param2) !== FALSE &&
            strtotime($param1) < strtotime($param2)){

            $dateStart = $param1;
            $dateEnd = $param2;
        } else {
            $dateStart = date("Y-m-02", strtotime("- 1 month"));
            $dateEnd = date("Y-m-01");
        }

        $this->runMonth($dateStart, $dateEnd);
    }


    protected function monthCurrent()
    {
        if(date("d") > 1){
            $dateStart = date("Y-m-02");
            $dateEnd = date("Y-m-d");
        } else {
            $dateStart = date("Y-m-02", strtotime("- 1 month"));
            $dateEnd = date("Y-m-01");
        }

        $this->runMonth($dateStart, $dateEnd);
    }


    protected function runMonth($dateStart, $dateEnd)
    {
        $month = date("n", strtotime($dateStart));
        $year = date("Y", strtotime($dateEnd));

        $result = [
            'count' => 0,
            'date_start' => $dateStart,
            'date_end' => $dateEnd,
        ];

        $sqlDB = DB::connection('mysql');
        $sqlDB->table('qb_advertiser_report')
            ->select('advertiser_id', DB::raw('SUM(amount) as amount'))
            ->where('type', 1)
            ->where('date', '>=', $dateStart)
            ->where('date', '<=', $dateEnd)
            ->orderBy('advertiser_id')
            ->groupBy('advertiser_id')
            ->chunk(50, function ($dataReport) use (&$result, &$sqlDB, &$month, &$year) {

                foreach ($dataReport as $report) {

                    $data = modelQBReportMonth::where('advertiser_id', $report->advertiser_id)
                        ->where('type', 1)
                        ->where('year', $year)
                        ->where('month', $month)
                        ->first();

                    if(!$data){
                        $data = new modelQBReportMonth();
                        $data->fill([
                            'advertiser_id' => $report->advertiser_id,
                            'type' => 1,
                            'date' => "$year-$month-02",
                            'month' => $month,
                            'year' => $year
                        ]);
                    }

                    $data->amount = $report->amount;
                    $data->save();

                    $result['count'] ++;
                }
            });


        var_dump($result);
    }


    protected function generate()
    {
        $result = [
            'count' => 0,
        ];

        $sqlDB = DB::connection('mysql');

        modelAdvertiser::orderBy('id')
            ->chunk(50, function ($dataAdvertiser) use (&$result, &$sqlDB) {

                foreach ($dataAdvertiser as $advertiser) {

                    $data = modelCreditCap::firstOrCreate(['advertiser_id' => $advertiser->id]);

                    if($advertiser->prepay){
                        $data->fill([
                            'revenue' => 0,
                            'revenue_mtd' => 0,
                            'balance' => 0,
                            'cap' => 0,
                            'cap_percent' => 0,
                            'cap_type' => 0,
                            'ar' => 0,
                            'is_6_month' => 0,
                            'num_month' => 0
                        ]);
                        $data->save();

                        continue;
                    }

                    $modelReport = new modelQBReport();
                    $modelStat = new modelAdvertiserStat();
                    $modelMonth = new modelQBReportMonth();

                    $dataStat = $modelStat->getData($advertiser->id);
                    $dataStatMonth = $modelStat->getDataByCurrentMonth($advertiser->id);
                    $dataMonth = $modelMonth->getInvoiceByAdvertiser($advertiser->id);

                    $dataCustomer = modelCustomer::where('advertiser_id', $advertiser->id)->first();
                    if($dataCustomer){
                        $ar = $dataCustomer->ar;
                    } else {
                        $ar = 0;
                    }

                    if($advertiser->cap){
                        $data->cap_type = 1;
                        $data->cap = $advertiser->cap;
                    } else {
                        $data->cap_type = 2;
                    }

                    if($dataMonth['count']){
                        if(!$data->cap_type){
                            $data->cap_type = 2;
                        }
                    }

                    if($data->cap_type == 1){
                        if($dataMonth['count'] >= 6){
                            $data->cap = $dataMonth['avg'];
                            $data->cap_type = 2;

                            $advertiser->cap = NULL;
                            $advertiser->save();
                        }
                    } else if($data->cap_type == 2) {
                        $data->cap = $dataMonth['avg'];
                    } else {
                        $data->cap = $dataMonth['avg'];
                    }

                    $data->is_6_month = $dataMonth['count'] >= 6 ? 1 : 0;
                    $data->num_month = $dataMonth['count'];

                    if($dataStat) {
                        $data->revenue = $dataStat->revenue ? : 0;
                    } else {
                        $data->revenue = 0;
                    }
                    if($dataStatMonth) {
                        $data->revenue_mtd = $dataStatMonth->revenue ? : 0;
                    } else {
                        $data->revenue_mtd = 0;
                    }

                    $data->ar = $ar;
                    $data->balance = $ar + $data->revenue_mtd;

                    if($data->balance && $data->cap){
                        $data->cap_percent = round((($data->balance / $data->cap) * 100), 2);
                    } else {
                        $data->cap_percent = 0;
                    }

                    $data->save();

                    $result['count'] ++;
                }
            });

        var_dump($result);
    }


    protected function notifyLimit()
    {
        $result = ['count' => 0];

        modelCreditCap::where('cap_percent', '<', 80)->where('notify_limit', 1)->update(['notify_limit' => 0]);

        modelCreditCap::where('cap_percent', '>', 80)->where('notify_limit', 0)->orderBy('id')
            ->chunk(50, function ($dataCreditCap) use (&$result) {

                foreach ($dataCreditCap as $data){

                    $mailer = new Mailer();

                    $send = $mailer->sendCreditCapLimit($data);
                    if($send){
                        $data->notify_limit = 1;
                        $data->save();

                        $result['count'] ++;
                    }
                }

            });

        var_dump($result);
    }

}