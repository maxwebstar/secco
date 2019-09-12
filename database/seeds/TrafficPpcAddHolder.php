<?php

use Illuminate\Database\Seeder;

class TrafficPpcAddHolder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('traffic_ppc')->where('name', 'ALL')->update(['place_holder' => 'tf_ppc_all']);
        DB::table('traffic_ppc')->where('name', 'Auto-Dial')->update(['place_holder' => 'tf_ppc_aut']);
        DB::table('traffic_ppc')->where('name', 'Yellow Pages/Call Directory')->update(['place_holder' => 'tf_ppc_yellow']);
        DB::table('traffic_ppc')->where('name', 'Newspaper/Print Ads')->update(['place_holder' => 'tf_ppc_news']);
        DB::table('traffic_ppc')->where('name', 'Television')->update(['place_holder' => 'tf_ppc_telev']);
        DB::table('traffic_ppc')->where('name', 'Radio')->update(['place_holder' => 'tf_ppc_rad']);
        DB::table('traffic_ppc')->where('name', 'Billboard')->update(['place_holder' => 'tf_ppc_bill']);
        DB::table('traffic_ppc')->where('name', 'Media Streaming')->update(['place_holder' => 'tf_ppc_media']);
        DB::table('traffic_ppc')->where('name', 'Call Center')->update(['place_holder' => 'tf_ppc_call']);
        DB::table('traffic_ppc')->where('name', 'YouTube/Online Video')->update(['place_holder' => 'tf_ppc_youtube']);
    }
}
