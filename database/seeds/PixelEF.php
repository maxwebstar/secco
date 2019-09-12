<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PixelEF extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

//        DB::table('pixel')->where('id', '<=', 8)->where('is_lt', 0)->update(['is_lt' => 1]);
//
//        DB::table('pixel')->where('name', 'Secure JS')->whereNull('ef_key')->update(['ef_key' => 'javascript', 'is_ef' => 1]);
//        DB::table('pixel')->where('name', 'Secure IMG')->whereNull('ef_key')->update(['ef_key' => 'cookie_based', 'is_ef' => 1]);
//        DB::table('pixel')->where('name', 'Secure S2S')->whereNull('ef_key')->update(['ef_key' => 'server_postback', 'is_ef' => 1]);
//        DB::table('pixel')->where('name', 'iFrame')->whereNull('ef_key')->update(['ef_key' => 'https_iframe_pixel', 'is_ef' => 1]);
//
//        DB::table('pixel')->insert([
//            'id' => 9,
//            'key' => null,
//            'ef_key' => 'http_image_pixel',
//            'name' => 'HTTP Image',
//            'position' => 9,
//            'show' => 1,
//            'is_lt' => 0,
//            'is_ef' => 1,
//            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
//            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
//        ]);
//        DB::table('pixel')->insert([
//            'id' => 10,
//            'key' => null,
//            'ef_key' => 'https_image_pixel',
//            'name' => 'HTTPS Image',
//            'position' => 10,
//            'show' => 1,
//            'is_lt' => 0,
//            'is_ef' => 1,
//            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
//            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
//        ]);
//        DB::table('pixel')->insert([
//            'id' => 11,
//            'key' => null,
//            'ef_key' => 'http_iframe_pixel',
//            'name' => 'HTTP Iframe',
//            'position' => 11,
//            'show' => 1,
//            'is_lt' => 0,
//            'is_ef' => 1,
//            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
//            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
//        ]);

        DB::table('pixel')->where('name', 'HTTPS Image')->update(['ef_key' => 'cookie_based', 'is_ef' => 1]);
    }
}
