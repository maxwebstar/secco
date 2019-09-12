<?php

use Illuminate\Database\Seeder;

class TermTemplateGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql = "
            INSERT INTO `term_template_group` (`id`, `display_name`, `position`, `by_default`, `show`, `created_at`, `updated_at`) VALUES
            (1, 'Adult/Incent', 1, 0, 1, '2018-06-04 18:05:54', '2018-06-04 18:05:54'),
            (2, 'Restricted', 2, 1, 1, '2018-06-04 18:06:20', '2018-06-04 18:09:39'),
            (3, 'Notes', 3, 0, 1, '2018-06-04 18:06:38', '2018-06-04 18:06:38');
        ";

        DB::statement($sql);
    }
}
