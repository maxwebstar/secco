<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PixelTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'JavaScript' => 'JS', /*Javasript*/
            'Image' => 'IMG',
            'ServerSide' => 'S2S',
            'sjs' => 'Secure JS',
            'simg' => 'Secure IMG',
            'sServerSide' => 'Secure S2S',
            'mindspark' => 'Mindspark',
        ];

        $count = 1;

        foreach($data as $key => $name){

            DB::table('pixel')->insert([
                'key' => $key,
                'name' => $name,
                'show' => 1,
                'position' => $count,
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            $count ++;
        }
    }
}
