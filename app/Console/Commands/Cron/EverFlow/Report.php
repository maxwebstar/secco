<?php

namespace App\Console\Commands\Cron\EverFlow;

use Illuminate\Console\Command;

use App\Services\EverFlow\General as EF_General;
use App\Services\EverFlow\Offer as EF_Offer;

use App\Models\OfferReport as modelOfferReport;
use App\Models\Network as modelNetwork;
use App\Models\Offer as modelOffer;

use Validator;

use DateTime;
use DateTimeZone;
use Exception;

class Report extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron/everflow:report {type} {param1?} {param2?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load report';


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
            case "manual" :
                $this->manual();
                break;
            case "yesterday" :
                $this->yesterday();
                break;
            case "day-3-ago" :
                $this->day3ago();
                break;
            default :
                throw new Exception('Empty type commaand.');
                break;
        }
    }


    protected function manual()
    {
        $dateStart = $this->argument('param1');
        $dateEnd = $this->argument('param2');

        if(DateTime::createFromFormat('Y-m-d', $dateStart) === FALSE){
            throw new Exception("Invalid date start from, correct form = 'YYYY-mm-dd'");
        }
        if(DateTime::createFromFormat('Y-m-d', $dateEnd) === FALSE){
            throw new Exception("Invalid date end from, correct form = 'YYYY-mm-dd'");
        }

        if(strtotime($dateEnd) <= strtotime($dateStart)){
            throw new Exception("Invalid range for date start and date end");
        }

        $dateStart = new DateTime($dateStart, new DateTimeZone("America/New_York"));
        $dateEnd = new DateTime($dateEnd, new DateTimeZone("America/New_York"));


        while($dateStart <= $dateEnd){

            $date = $dateStart->format("Y-m-d");
            var_dump($date);

            $ef_Offer = new EF_Offer();
            $dataReport = $ef_Offer->getStat($date, $date);

            if($dataReport){
                $this->loadData($dataReport);
            }

            $dateStart->modify('+1 day');
        }
    }


    protected function yesterday()
    {
        $dateStart = date('Y-m-d', strtotime('-1 day'));
        $dateEnd = date('Y-m-d', strtotime('-1 day'));

        $ef_Offer = new EF_Offer();
        $dataReport = $ef_Offer->getStat($dateStart, $dateEnd);

        if($dataReport){
            $this->loadData($dataReport);
        }
    }


    protected function day3ago()
    {
        $dateStart = date('Y-m-d', strtotime('-4 day'));
        $dateEnd = date('Y-m-d', strtotime('-1 day'));

        $ef_Offer = new EF_Offer();
        $dataReport = $ef_Offer->getStat($dateStart, $dateEnd);

        if($dataReport){
            $this->loadData($dataReport);
        }
    }


    protected function loadData($dataReport)
    {
        $result = ['create' => 0, 'update' => 0];

        if($dataReport){

            $dataNetwork = modelNetwork::where('short_name', 'EF')->first();

            foreach ($dataReport->table as $iter) {

                $date = date('Y-m-d', $iter->columns[1]->id);

                $dataOffer = modelOffer::where('ef_id', $iter->columns[0]->id)->first();

                $exist = modelOfferReport::where('network_id', $dataNetwork->id)
                    ->where('date', $date)
                    ->where('offer_network_id', $iter->columns[0]->id)
                    ->first();

                if ($dataOffer) {
                    $offer_id = $dataOffer->id;
                } else {
                    $offer_id = 0;
                }

                if ($exist) {

                    $exist->fill([
                        'imp' => $iter->reporting->imp,
                        'total_click' => $iter->reporting->total_click,
                        'unique_click' => $iter->reporting->unique_click,
                        'approved' => $iter->reporting->cv,
                        'revenue' => $iter->reporting->revenue,
                        'payout' => $iter->reporting->payout,
                        'profit' => $iter->reporting->profit,
                        'margin' => $iter->reporting->margin,
                    ]);
                    $exist->save();

                    $result['update']++;

                } else {

                    $data = new modelOfferReport();
                    $data->fill([
                        'network_id' => $dataNetwork->id,
                        'offer_network_id' => $iter->columns[0]->id,
                        'date' => $date,
                        'imp' => $iter->reporting->imp,
                        'total_click' => $iter->reporting->total_click,
                        'unique_click' => $iter->reporting->unique_click,
                        'approved' => $iter->reporting->cv,
                        'revenue' => $iter->reporting->revenue,
                        'payout' => $iter->reporting->payout,
                        'profit' => $iter->reporting->profit,
                        'margin' => $iter->reporting->margin,
                        'offer_id' => $offer_id,
                        'lt_id' => 0,
                        'ef_id' => $iter->columns[0]->id,
                    ]);
                    $data->save();

                    $result['create']++;
                }
            }
        }

        var_dump($result);
    }

}