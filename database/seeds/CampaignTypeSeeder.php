<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CampaignTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('campaign_type')->insert([
            'key' => 'CPA',
            'name' => 'CPA',
            'show' => 1,
            'position' => 1,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        DB::table('campaign_type')->insert([
            'key' => 'CPL',
            'name' => 'CPL',
            'show' => 1,
            'position' => 2,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        DB::table('campaign_type')->insert([
            'key' => 'CPC',
            'name' => 'CPC',
            'show' => 1,
            'position' => 3,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        DB::table('campaign_type')->insert([
            'key' => 'CPM',
            'name' => 'CPM',
            'show' => 1,
            'position' => 4,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }
}
