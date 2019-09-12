<?php

use Illuminate\Database\Seeder;

class NetworkTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('network')->insert([
            'network_id' => 1,
            'short_name' => 'LT',
            'display_name' => 'LinkTrust',
            'field_name' => 'lt_id',
            'position' => 1,
            'by_default' => 0,
            'show' => 1,
        ]);
        DB::table('network')->insert([
            'network_id' => 2,
            'short_name' => 'EF',
            'display_name' => 'EverFlow',
            'field_name' => 'ef_id',
            'position' => 2,
            'by_default' => 1,
            'show' => 1,
        ]);
    }
}
