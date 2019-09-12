<?php

use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql = "
            INSERT INTO `permissions` (`id`, `group_id`, `name`, `display_name`, `description`, `position`, `show`, `created_at`, `updated_at`) VALUES
            (1, 13, 'user_access', 'Access', NULL, 1, 1, '2018-05-10 23:52:19', '2018-05-11 00:36:46'),
            (6, 13, 'user_search', 'Search', NULL, 2, 1, '2018-05-11 00:29:52', '2018-05-11 00:37:06'),
            (7, 13, 'user_edit', 'Edit', NULL, 3, 1, '2018-05-11 00:30:27', '2018-05-11 00:37:09'),
            (8, 13, 'user_create', 'Create', NULL, 4, 1, '2018-05-11 00:30:52', '2018-05-11 00:37:12'),
            (9, 13, 'user_delete', 'Delete', NULL, 5, 1, '2018-05-11 00:31:17', '2018-05-11 00:37:27'),
            (10, 13, 'user_change_password', 'Change password', NULL, 6, 1, '2018-05-11 00:31:49', '2018-05-11 00:37:30'),
            (12, 10, 'dashboard_access', 'Access', NULL, 1, 1, '2018-05-11 00:46:22', '2018-05-11 00:49:56'),
            (13, 10, 'dashboard_linktrust', 'LinkTrust', NULL, 2, 1, '2018-05-11 00:47:31', '2018-05-11 00:47:31'),
            (14, 11, 'permission_access', 'Access', NULL, 1, 1, '2018-05-11 00:48:43', '2018-05-11 00:48:43');
        ";

        DB::statement($sql);
    }
}
