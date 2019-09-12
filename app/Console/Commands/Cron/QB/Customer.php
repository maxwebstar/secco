<?php
namespace App\Console\Commands\Cron\QB;

use Illuminate\Console\Command;

use App\Services\QB\Core as QB_Core;

use App\Models\User as modelUser;
use App\Models\Advertiser as modelAdvertiser;
use App\Models\QB\Customer as modelQBCustomer;

use Validator;
use DB;

use DateTime;
use DateTimeZone;

use Exception;
use PDOException;


class Customer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron/qb:customer {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync customer to advertiser';

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
            case "connect" :
                $this->connect();
                break;
            case "import" :
                $this->import();
                break;
            default :
                throw new Exception('Empty type commaand.');
                break;
        }
    }


    public function import()
    {
        $result = [
            'create' => 0,
            'exist' => 0,
        ];

        $qbCore = new QB_Core();

        $dataService = $qbCore->getDataService();

        $sql = "SELECT * FROM Customer";

        $data = $dataService->Query($sql);
        if($data) {
            foreach ($data as $iter) {

                $data = modelQBCustomer::where('quickbook_id', $iter->Id)->first();
                if(!$data){

                    $data = new modelQBCustomer();
                    $data->fill([
                        'quickbook_id' => $iter->Id,
                        'ar' => $iter->Balance,
                        'name' => $iter->FullyQualifiedName,
                        'email' => isset($iter->PrimaryEmailAddr->Address) ? $iter->PrimaryEmailAddr->Address : NULL,
                        'phone' => isset($iter->PrimaryPhone->FreeFormNumber) ? $iter->PrimaryPhone->FreeFormNumber : NULL,
                        'company' => $iter->CompanyName,
                        'active' => $iter->Active ? 1 : 0,
                        'created_qb' => date("Y-m-d H:i:s", strtotime($iter->MetaData->CreateTime)),
                        'status' => 1,
                    ]);
                    $data->save();

                    $result['create'] ++;

                } else {

                    $data->ar = $iter->Balance;
                    $data->name = $iter->FullyQualifiedName;
                    $data->email = isset($iter->PrimaryEmailAddr->Address) ? $iter->PrimaryEmailAddr->Address : $data->email;
                    $data->phone = isset($iter->PrimaryPhone->FreeFormNumber) ? $iter->PrimaryPhone->FreeFormNumber : $data->phone;
                    $data->company = $iter->CompanyName;
                    $data->active = $iter->Active ? 1 : 0;
                    $data->save();

                    $result['exist'] ++;
                }
            }
        }

        var_dump($result);
    }


    protected function connect()
    {
        $qbCore = new QB_Core();

        $dataService = $qbCore->getDataService();

        $sql = "SELECT * FROM Customer";

        $data = $dataService->Query($sql);

        if($data){
            foreach($data as $iter){

                dd($iter);

                $search = modelAdvertiser::where('name', $iter->FullyQualifiedName)->get();
                if($search->count() == 1){

                    $search[0]->quickbook_id = $iter->Id;
                    $search[0]->save();
                }
            }
        }
    }

}