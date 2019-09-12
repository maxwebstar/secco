<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

use App\Models\CampaignType as modelCampaignType;

class CampaignTypeEF extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        modelCampaignType::where('id', 1)->update(['ef_key' => 'cpa', 'is_lt' => 1, 'is_ef' => 1]);
        modelCampaignType::where('id', 2)->update(['is_lt' => 1]);
        modelCampaignType::where('id', 3)->update(['ef_key' => 'cpc', 'is_lt' => 1, 'is_ef' => 1]);
        modelCampaignType::where('id', 4)->update(['ef_key' => 'cpm', 'is_lt' => 1, 'is_ef' => 1]);

        DB::table('campaign_type')->insert([
            'id' => 5,
            'key' => 'CPS',
            'ef_key' => 'cps',
            'name' => 'CPS',
            'show' => 1,
            'position' => 5,
            'is_lt' => 0,
            'is_ef' => 1,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        DB::table('campaign_type')->insert([
            'id' => 6,
            'key' => 'PRV',
            'ef_key' => 'prv',
            'name' => 'PRV',
            'show' => 1,
            'position' => 6,
            'is_lt' => 0,
            'is_ef' => 1,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }
}
