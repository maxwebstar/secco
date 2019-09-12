<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class QBReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $arr = [
//            1 => ["date" => "2018-01-02", "amount" => 15],
//            2 => ["date" => "2018-01-15", "amount" => 150],
//            3 => ["date" => "2018-03-01", "amount" => 20],
//            4 => ["date" => "2018-03-02", "amount" => 25],
//            5 => ["date" => "2018-03-20", "amount" => 30],
//            6 => ["date" => "2018-04-02", "amount" => 50],
//            7 => ["date" => "2018-04-10", "amount" => 115],
//            8 => ["date" => "2018-07-20", "amount" => 515],
//            9 => ["date" => "2018-10-15", "amount" => 350],
        ];

//        foreach($arr as $key => $iter){
//
//            DB::table('qb_advertiser_report')->insert([
//                'advertiser_id' => 49,
//                'quickbook_id' => $key,
//                'currency_id' => 1,
//                'amount' => $iter['amount'],
//                'type' => 1,
//                'date' => $iter['date'],
//                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
//                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
//            ]);
//        }

        $arr = [
            10 => ["date" => "2018-01-02", "amount" => 150],
            11 => ["date" => "2018-01-15", "amount" => 150],
            12 => ["date" => "2018-03-01", "amount" => 200],
            13 => ["date" => "2018-03-02", "amount" => 25],
            14 => ["date" => "2018-03-20", "amount" => 30],
            15 => ["date" => "2018-04-02", "amount" => 50],
            16 => ["date" => "2018-04-10", "amount" => 15],
            17 => ["date" => "2018-07-20", "amount" => 215],
            18 => ["date" => "2018-09-02", "amount" => 150],
            19 => ["date" => "2018-10-10", "amount" => 180],
        ];

        foreach($arr as $key => $iter){

            DB::table('qb_advertiser_report')->insert([
                'advertiser_id' => 448,
                'quickbook_id' => $key,
                'currency_id' => 1,
                'amount' => $iter['amount'],
                'type' => 2,
                'date' => $iter['date'],
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
        }
    }
}
