<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CapUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('cap_unit')->insert([
            'key' => 'monetary',
            'name' => 'Monetary',
            'show' => 1,
            'position' => 1,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        DB::table('cap_unit')->insert([
            'key' => 'lead',
            'name' => 'Lead',
            'show' => 1,
            'position' => 2,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }
}
