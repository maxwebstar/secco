<?php
namespace App\Console\Commands\Cron\QB;

use Illuminate\Console\Command;

use App\Services\QB\Core as QB_Core;

use App\Models\User as modelUser;
use App\Models\Advertiser as modelAdvertiser;
use App\Models\QB\Report as modelQBReport;
use App\Models\Currency as modelCurrency;
use App\Models\QB\Customer as modelCustomer;

use Validator;
use DB;

use DateTime;
use DateTimeZone;

use Exception;
use PDOException;


class Payment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron/qb:payment {type} {date_start?} {date_end?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get payment from qb';

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
            'count' => 0,
            'sum' => 0,
            'create' => 0,
            'update' => 0,
            'missing_advertiser',
            'missing_sum' => 0
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

        switch ($type) {
            case "manual" :
                $this->manual();
                break;
            case "yesterday" :
                $this->yesterday();
                break;
            case "today" :
                $this->today();
                break;
            default :
                throw new Exception('Empty type commaand.');
                break;
        }
    }


    protected function today()
    {
        $dateStart = date("Y-m-d 00:00:00");
        $dateEnd = date("Y-m-d H:i:s");

        $dateStart = date('c', strtotime($dateStart));
        $dateEnd = date('c', strtotime($dateEnd));

        $qbCore = new QB_Core();
        $dataService = $qbCore->getDataService();

        $sql = "SELECT * FROM Payment WHERE MetaData.CreateTime >= '$dateStart' AND MetaData.CreateTime <= '$dateEnd'";
        $data = $dataService->Query($sql);

        if ($data) {
            foreach ($data as $iter) {

                $this->saveData($iter);
            }
        }

        $this->showResult();
    }


    protected function yesterday()
    {
        $dateStart = date("Y-m-d 00:00:00", strtotime("-1 day"));
        $dateEnd = date("Y-m-d 23:59:59", strtotime("-1 day"));

        $dateStart = date('c', strtotime($dateStart));
        $dateEnd = date('c', strtotime($dateEnd));

        $qbCore = new QB_Core();
        $dataService = $qbCore->getDataService();

        $sql = "SELECT * FROM Payment WHERE MetaData.CreateTime >= '$dateStart' AND MetaData.CreateTime <= '$dateEnd'";
        $data = $dataService->Query($sql);

        if ($data) {
            foreach ($data as $iter) {

                $this->saveData($iter);
            }
        }

        $this->showResult();
    }


    protected function manual()
    {
        $dateStart = $this->argument('date_start');
        $dateEnd = $this->argument('date_end');

        if (DateTime::createFromFormat('Y-m-d', $dateStart) == FALSE) {
            throw new Exception('Invalid format for Date Start (correct format yyyy-mm-dd)');
        }
        if (DateTime::createFromFormat('Y-m-d', $dateEnd) == FALSE) {
            throw new Exception('Invalid format for Date End (correct format yyyy-mm-dd)');
        }
        if (strtotime($dateStart) > strtotime($dateEnd)) {
            throw new Exception('Invalid date range (correct range Date End > Date Start)');
        }

        $dateStart = date('c', strtotime($dateStart));
        $dateEnd = date('c', strtotime($dateEnd));

        $qbCore = new QB_Core();
        $dataService = $qbCore->getDataService();

        $sql = "SELECT * FROM Payment WHERE MetaData.CreateTime >= '$dateStart' AND MetaData.CreateTime <= '$dateEnd'";
        $data = $dataService->Query($sql);

        if ($data) {
            foreach ($data as $iter) {

                $this->saveData($iter);
            }
        }

        $this->showResult();
    }


    protected function saveData($dataQB)
    {
        $this->result['count'] ++;
        $this->result['sum'] += $dataQB->TotalAmt;

        $date = date("Y-m-d", strtotime($dataQB->TxnDate));
        $created = date("Y-m-d H:i:s", strtotime($dataQB->MetaData->CreateTime));
        $updated = date("Y-m-d H:i:s", strtotime($dataQB->MetaData->LastUpdatedTime));

        $dataCurrency = modelCurrency::where('key', $dataQB->CurrencyRef)->first();

        $dataReport = modelQBReport::where('type', 2)
            ->where('date', $date)
            ->where('quickbook_id', $dataQB->Id)
            ->first();

        if(!$dataReport){

            $dataCustomer = modelCustomer::where('quickbook_id', $dataQB->CustomerRef)->where('advertiser_id', '>', 0)->first();
            if($dataCustomer){

                $dataReport = new modelQBReport();
                $dataReport->fill([
                    'advertiser_id' => $dataCustomer->advertiser_id,
                    'quickbook_id' => $dataQB->Id,
                    'currency_id' => $dataCurrency->id,
                    'amount' => $dataQB->TotalAmt,
                    'type' => 2,
                    'date' => $date,
                    'created_qb' => $created,
                ]);

                if(isset($dataQB->DocNumber) && $dataQB->DocNumber){
                    $dataReport->qb_number = $dataQB->DocNumber;
                }

                $dataReport->save();

                $this->result['create'] ++;
            } else {
                $this->result['missing_advertiser'][$dataQB->CustomerRef] = $dataQB->TotalAmt;
                $this->result['missing_sum'] += $dataQB->TotalAmt;
            }

        } else {

            if(isset($dataQB->DocNumber) && $dataQB->DocNumber && !$dataReport->qb_number){
                $dataReport->qb_number = $dataQB->DocNumber;
            }

            $dataReport->currency_id = $dataCurrency->id;
            $dataReport->amount = $dataQB->TotalAmt;
            $dataReport->updated_qb = $updated;
            $dataReport->save();

            $this->result['update'] ++;
        }
    }


    protected function showResult()
    {
//        var_dump("missing_advertiser " . count($this->result['missing_advertiser']));
//
//        unset($this->result['missing_advertiser']);

        var_dump($this->result);
    }

}