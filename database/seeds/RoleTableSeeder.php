<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'name' => 'user',
            'display_name' => 'User',
            'description' => null,
            'priority' => 10,
            'position' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('roles')->insert([
            'name' => 'sales',
            'display_name' => 'Sales',
            'description' => null,
            'priority' => 20,
            'position' => 2,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('roles')->insert([
            'name' => 'account_manager',
            'display_name' => 'Account Manager',
            'description' => null,
            'priority' => 30,
            'position' => 3,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        DB::table('roles')->insert([
            'name' => 'accounting',
            'display_name' => 'Accounting',
            'description' => null,
            'priority' => 40,
            'position' => 5,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('roles')->insert([
            'name' => 'ad_ops',
            'display_name' => 'Ad Ops',
            'description' => null,
            'priority' => 50,
            'position' => 6,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
        DB::table('roles')->insert([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => null,
            'priority' => 60,
            'position' => 7,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

    }
}
