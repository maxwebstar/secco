<?php

namespace App\Console\Commands\Cron\LinkTrust;

use Illuminate\Console\Command;

use App\Models\AdvertiserStat as modelAdvertiserStat;
use App\Models\Advertiser as modelAdvertiser;
use App\Models\User as modelUser;

use App\Services\LinkTrust\Advertiser as LT_Advertiser;

use DateTime;
use DateTimeZone;

class Advertiser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron/linktrust:advertiser {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronization of advertisers';

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
            case "sync-statistic-yesterday" :
                $this->syncStatisticYesterday();
                break;
            default :
                throw new Exception('Empty type commaand.');
                break;
        }
    }


    protected function syncStatisticYesterday()
    {

        $count = [
            'revenue' => 0,
            'lost_revenue' => 0,
            'lost_count' => 0,
            'lost_advertiser' => [],
        ];

        $modelAdvertiser = new modelAdvertiser();

        $dateStart = new DateTime('NOW', new DateTimeZone("America/New_York"));
        $dateEnd = new DateTime('NOW', new DateTimeZone("America/New_York"));

        $dateStart->modify('-1 day');

        $LT_Advertiser = new LT_Advertiser();

        $ltStat = $LT_Advertiser->getStat($dateStart->format('n/j/Y'), $dateEnd->format('n/j/Y'));

        if($ltStat){
            foreach($ltStat->Merchant as $iter){

                $lt_id = (int) $iter->attributes()->Id;
                $date = $dateStart->format('Y-m-d');

                $revenue = str_replace("$", "", $iter->Statistics->Revenue);
                $commision = str_replace("$", "", $iter->Statistics->Commission);
                $margin = str_replace("$", "", $iter->Statistics->Margin);

                $dataAdvertiser = modelAdvertiser::where('lt_id', $lt_id)->first();

                if($dataAdvertiser){

                    $dataStat = modelAdvertiserStat::where('date', $date)
                        ->where('network_id', 1)
                        ->where('advertiser_id', $dataAdvertiser->id)
                        ->where('lt_id', $lt_id)
                        ->first();

                    if($dataStat){
                        $dataStat->approved = $iter->Statistics->Approved;
                        $dataStat->click = $iter->Statistics->Clicks;
                        $dataStat->revenue = $revenue;
                        $dataStat->payout = $commision;
                        $dataStat->profit = $margin;
                        $dataStat->save();
                    } else {
                        modelAdvertiserStat::create([
                            'advertiser_id' => $dataAdvertiser->id,
                            'lt_id' => $lt_id,
                            'network_id' => 1,
                            'date' => $date,
                            'approved' => $iter->Statistics->Approved,
                            'click' => $iter->Statistics->Clicks,
                            'revenue' => $revenue,
                            'payout' => $commision,
                            'profit' => $margin,
                        ]);
                    }

                    $count['revenue'] += $revenue;
                } else {
                    $count['lost_revenue'] += $revenue;
                    $count['lost_advertiser'][] = $lt_id;
                    $count['lost_count'] ++;
                }
            }
        }

        var_dump($count);
    }

}
