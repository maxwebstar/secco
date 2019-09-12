<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CapTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'perday' => 'Per Day',
            'perweek' => 'Per Week',
            'permonth' => 'Per Month',
            'total' => 'Total',
        ];

        $count = 0;

        foreach($data as $key => $name){

            DB::table('cap_type')->insert([
                'key' => $key,
                'name' => $name,
                'show' => 1,
                'position' => $count,
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            $count ++;
        }
    }
}
