<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('currency')->insert([
            'id' => 1,
            'name' => 'US Dollar',
            'key' => 'USD',
            'sign' => '$',
            'position' => 1,
            'show' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('currency')->insert([
            'id' => 2,
            'name' => 'EURO',
            'key' => 'EUR',
            'sign' => '€',
            'position' => 2,
            'show' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('currency')->insert([
            'id' => 3,
            'name' => 'Pound Sterling(British Pounds)',
            'key' => 'GBP',
            'sign' => '£',
            'position' => 3,
            'show' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }
}
