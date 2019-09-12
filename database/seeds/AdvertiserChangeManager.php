<?php

use Illuminate\Database\Seeder;

class AdvertiserChangeManager extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql = "ALTER TABLE `advertiser` CHANGE `manager_id` `manager_id` INT(11) NULL;";
        $sql2 = "ALTER TABLE `advertiser` DROP INDEX `advertiser_email_unique`;";

        DB::statement($sql);
        DB::statement($sql2);
    }
}
