<?php

use Illuminate\Database\Seeder;

use App\Models\Advertiser as modelAdvertiser;
use App\Models\AdvertiserStat as modelAdvertiserStat;

class LTStatistic extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $result = [
            'create' => 0,
            'update' => 0,
        ];

        $statistic = [
            'anastasia' => [
                0 => [
                    'date' => '2018-11-11',
                    'revenue' => 784525
                ],
            ],
            'turnmear' => [
                0 => [
                    'date' => '2018-11-11',
                    'revenue' => 269291
                ],
            ]
        ];

        $advertiser['anastasia'] = modelAdvertiser::where('lt_id', 109867)->first();
        $advertiser['turnmear'] = modelAdvertiser::where('lt_id', 125960)->where('id', 1980)->first();

        foreach($advertiser as $name => $iter){

            if(isset($statistic[$name])){
                foreach($statistic[$name] as $stat){

                    var_dump($name . ' ' . $stat['date']);

                    $data = modelAdvertiserStat::where('advertiser_id', $iter->id)->where('network_id', 1)->where('date', $stat['date'])->first();
                    if(!$data){
                        $data = new modelAdvertiserStat();
                        $data->advertiser_id = $iter->id;
                        $data->date = $stat['date'];
                        $data->lt_id = $iter->lt_id;
                        $data->network_id = 1;
                        $data->click = 0;
                        $data->payout = 0;
                        $data->profit = 0;

                        $result['create'] ++;
                    } else {
                        $result['update'] ++;
                    }

                    $data->revenue = $stat['revenue'];
                    $data->save();
                }
            }
        }

        var_dump($result);
    }
}
