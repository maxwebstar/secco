<?php

use Illuminate\Database\Seeder;

class PermissionGroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql = "
            INSERT INTO `permissions_group` (`id`, `name`, `display_name`, `description`, `position`, `show`, `created_at`, `updated_at`) VALUES
            (1, 'accounting_section', 'Accounting section', NULL, 1, 1, '2018-05-10 22:31:24', '2018-05-10 22:31:24'),
            (2, 'report', 'Reports section', NULL, 2, 1, '2018-05-10 22:51:50', '2018-05-10 22:51:50'),
            (3, 'campaign', 'Campaigns section', NULL, 3, 1, '2018-05-10 22:52:18', '2018-05-10 22:52:18'),
            (4, 'affiliate', 'Affiliates section', NULL, 4, 1, '2018-05-10 22:52:55', '2018-05-10 22:52:55'),
            (5, 'advertiser', 'Advertisers section', NULL, 5, 1, '2018-05-10 22:53:36', '2018-05-10 23:01:33'),
            (6, 'quickbook', 'Quickbooks section', NULL, 6, 1, '2018-05-10 23:02:41', '2018-05-10 23:02:41'),
            (7, 'todo', 'Todos section', NULL, 7, 1, '2018-05-10 23:04:10', '2018-05-10 23:04:10'),
            (8, 'change_offer', 'Change Offer section', NULL, 8, 1, '2018-05-10 23:04:49', '2018-05-10 23:04:49'),
            (9, 'request', 'Request section', NULL, 9, 1, '2018-05-10 23:05:27', '2018-05-10 23:05:27'),
            (10, 'dashboard', 'Dashboard section', NULL, 10, 1, '2018-05-10 23:06:05', '2018-05-10 23:06:05'),
            (11, 'permission', 'Permissions section', NULL, 11, 1, '2018-05-10 23:06:47', '2018-05-10 23:06:47'),
            (12, 'profile', 'Profiles section', NULL, 12, 1, '2018-05-10 23:07:25', '2018-05-10 23:07:25'),
            (13, 'user', 'Users section', NULL, 13, 1, '2018-05-10 23:08:01', '2018-05-10 23:08:01'),
            (14, 'pricerequest', 'Pricerequest section', NULL, 14, 1, '2018-05-10 23:08:35', '2018-05-10 23:08:35'),
            (15, 'caprequest', 'Caprequest section', NULL, 15, 1, '2018-05-10 23:09:08', '2018-05-10 23:09:08'),
            (16, 'creative', 'Creative section', NULL, 16, 1, '2018-05-10 23:09:54', '2018-05-10 23:09:54');
        ";

        DB::statement($sql);
    }
}
