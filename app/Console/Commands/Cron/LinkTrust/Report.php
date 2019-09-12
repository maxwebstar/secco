<?php

namespace App\Console\Commands\Cron\LinkTrust;

use Illuminate\Console\Command;

use App\Services\LinkTrust\Offer as LT_Offer;

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
    protected $signature = 'cron/linktrust:report {type} {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load report';

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
            'create' => 0,
            'update' => 0,
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
        $date = $this->argument('date');

        $validator = Validator::make(['date' => $date], [
            'date' => 'required|date'
        ]);

        if($validator->fails()){
            var_dump($validator->errors());
            exit();
        }

        $dateCurrent = date('n/j/Y', strtotime($date));

        $this->loadData($dateCurrent);

        var_dump($this->result);
    }


    protected function yesterday()
    {
        $date = new DateTime();
        $date->modify('-1 day');

        $dateCurrent = $date->format('n/j/Y');

        $this->loadData($dateCurrent);

        var_dump($this->result);
    }


    protected function day3ago()
    {
        $dateStart = new DateTime();
        $dateStart->modify('-4 day');
        $dateEnd = new DateTime();
        $dateEnd->modify('-1 day');

        while($dateStart < $dateEnd){

            $dateCurrent = $dateStart->format('n/j/Y');

            $this->loadData($dateCurrent);

            $dateStart->modify('+1 day');
        }

        var_dump($this->result);
    }


    protected function loadData($date)
    {
        $dateEU = date('Y-m-d', strtotime($date));

        $lt_Offer = new LT_Offer();

        $dataStat = $lt_Offer->getStat($date, $date);

        if($dataStat){

            $dataNetwork = modelNetwork::where('short_name', 'LT')->first();

            foreach($dataStat->Campaign as $iter){

                $attr = $iter->attributes();

                $offer_lt_id = (string) $attr['Id'];
                $offer_lt_name = (string) $attr['Name'];

                $dataOffer = modelOffer::where('lt_id', $offer_lt_id)->first();

                $exist = modelOfferReport::where('network_id', $dataNetwork->id)
                    ->where('date', $dateEU)
                    ->where('offer_network_id', $offer_lt_id)
                    ->first();

                if ($dataOffer) {
                    $offer_id = $dataOffer->id;
                } else {
                    $offer_id = 0;
                }

                $total_click = $iter->Statistics->Clicks + $iter->Statistics->ClickGeoTargeted + $iter->Statistics->ClickDuplicate + $iter->Statistics->ClickExpired;

                $revenue = floatval(str_replace("$", "", $iter->Statistics->Revenue));
                $commission = floatval(str_replace("$", "", $iter->Statistics->Commission));
                $margin = floatval(str_replace("$", "", $iter->Statistics->Margin));
                $profit = $revenue - $commission;

                if ($exist) {

                    $exist->fill([
                        'imp' => $iter->Statistics->Impression,
                        'total_click' => $total_click,
                        'unique_click' => $iter->Statistics->Clicks,
                        'revenue' => $revenue,
                        'profit' => $profit,
                        'margin' => $margin,
                        'offer_id' => $offer_id,
                    ]);
                    $exist->save();

                    $this->result['update']++;

                } else {

                    $data = new modelOfferReport();
                    $data->fill([
                        'network_id' => $dataNetwork->id,
                        'offer_network_id' => $offer_lt_id,
                        'date' => $dateEU,
                        'imp' => $iter->Statistics->Impression,
                        'total_click' => $total_click,
                        'unique_click' => $iter->Statistics->Clicks,
                        'revenue' => $revenue,
                        'profit' => $profit,
                        'margin' => $profit,
                        'offer_id' => $offer_id,
                        'lt_id' => $offer_lt_id,
                        'ef_id' => 0,
                    ]);
                    $data->save();

                    $this->result['create']++;
                }
            }
        }
    }


}