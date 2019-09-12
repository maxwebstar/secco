<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class IOTemplateDocSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('io_template_doc')->insert([
            'name' => 'Net 15',
            'file_name' => null,
            'by_default' => 0,
            'position' => 1,
            'show' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('io_template_doc')->insert([
            'name' => 'Net 30',
            'file_name' => null,
            'by_default' => 0,
            'position' => 2,
            'show' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('io_template_doc')->insert([
            'name' => 'Net 45',
            'file_name' => null,
            'by_default' => 0,
            'position' => 3,
            'show' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('io_template_doc')->insert([
            'name' => 'Custom',
            'file_name' => null,
            'by_default' => 0,
            'position' => 4,
            'show' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

    }
}
