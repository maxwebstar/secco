<?php

use Illuminate\Database\Seeder;

class EmailTemplateGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql = "
            INSERT INTO `email_template_group` (`id`, `name`, `display_name`, `show`, `position`, `created_at`, `updated_at`) VALUES
            (1, 'advertiser_offer', 'Advertiser/Offer Set-Up', 1, 1, '2018-05-16 23:27:38', '2018-05-17 22:05:15'),
            (2, 'request', 'New Requests', 1, 2, '2018-05-16 23:28:07', '2018-05-16 23:29:43'),
            (3, 'chron', 'Chron Emails', 1, 3, '2018-05-16 23:28:31', '2018-05-16 23:28:31'),
            (4, 'finance', 'Finance', 1, 4, '2018-05-16 23:28:51', '2018-05-16 23:28:51');
        ";

        DB::statement($sql);
    }
}
