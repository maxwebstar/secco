<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OfferCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            "App CPE",
            "App Install",
            "Auto Insurance/Insurance",
            "Background Check Sale",
            "Biz Ops",
            "Cell Phone/Ringtones",
            "Contests",
            "Credit Card Offer/Signup",
            "Daily Deal/Coupons",
            "Dating",
            "Financial Services",
            "Gaming",
            "Gambling",
            "Health & Beauty",
            "Lead Gen",
            "Life Insurance",
            "Market Research/Surveys",
            "Mobile",
            "Mobile Dating",
            "Mobile Lead Gen",
            "Mobile Gaming",
            "Mortgage",
            "Pin Submit",
            "Software/Download",
            "Sweepstakes",
        ];

        $count = 1;

        foreach($data as $iter){
            DB::table('offer_category')->insert([
                'name' => $iter,
                'show' => 1,
                'position' => $count,
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            $count ++;
        }
    }
}
