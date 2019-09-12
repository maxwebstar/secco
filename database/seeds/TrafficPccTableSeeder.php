<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TrafficPccTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('traffic_ppc')->insert([
            'name' => 'All',
            'value' => 'all',
            'show' => 1,
            'position' => 1,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('traffic_ppc')->insert([
            'name' => 'Auto-Dial',
            'value' => 'auto-dial',
            'show' => 1,
            'position' => 2,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('traffic_ppc')->insert([
            'name' => 'Yellow Pages/Call Directory',
            'value' => 'yellow pages/call directory',
            'show' => 1,
            'position' => 3,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('traffic_ppc')->insert([
            'name' => 'Newspaper/Print Ads',
            'value' => 'newspaper/print ads',
            'show' => 1,
            'position' => 4,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('traffic_ppc')->insert([
            'name' => 'Television',
            'value' => 'television',
            'show' => 1,
            'position' => 5,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('traffic_ppc')->insert([
            'name' => 'Radio',
            'value' => 'radio',
            'show' => 1,
            'position' => 6,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('traffic_ppc')->insert([
            'name' => 'Billboard',
            'value' => 'billboard',
            'show' => 1,
            'position' => 7,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('traffic_ppc')->insert([
            'name' => 'Media Streaming',
            'value' => 'media streaming',
            'show' => 1,
            'position' => 8,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('traffic_ppc')->insert([
            'name' => 'Call Center',
            'value' => 'call center',
            'show' => 1,
            'position' => 9,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('traffic_ppc')->insert([
            'name' => 'YouTube/Online Video',
            'value' => 'youtube/online video',
            'show' => 1,
            'position' => 10,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }
}
