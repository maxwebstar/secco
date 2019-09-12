<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class FrequencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('frequency')->insert([
            'id' => 1,
            'name' => 'Monthly',
            'lt_name' => null,
            'ef_name' => null,
            'position' => 1,
            'show' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('frequency')->insert([
            'id' => 2,
            'name' => 'Bi-Weekly',
            'lt_name' => null,
            'ef_name' => null,
            'position' => 2,
            'show' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('frequency')->insert([
            'id' => 3,
            'name' => 'Weekly',
            'lt_name' => null,
            'ef_name' => null,
            'position' => 3,
            'show' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('frequency')->insert([
            'id' => 4,
            'name' => 'Custom',
            'lt_name' => null,
            'ef_name' => null,
            'position' => 4,
            'show' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }
}
