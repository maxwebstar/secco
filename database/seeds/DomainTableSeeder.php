<?php

use Illuminate\Database\Seeder;

class DomainTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql = "
            INSERT INTO `domain` (`id`, `value`, `name`, `show`, `position`, `created_at`, `updated_at`) VALUES
            (1, 'ssmb', 'SSMB', 1, 1, '2018-05-15 22:08:16', '2018-05-15 22:08:16'),
            (2, 'sas', 'SAS', 1, 2, '2018-05-15 22:08:35', '2018-05-15 22:08:35'),
            (3, 'hqt', 'HQT', 1, 3, '2018-05-15 22:08:52', '2018-05-15 22:08:52'),
            (4, 'lxrb', 'LXRB', 1, 4, '2018-05-15 22:10:13', '2018-05-15 22:10:13'),
            (5, 'sat', 'SAT', 1, 5, '2018-05-15 22:10:28', '2018-05-15 22:10:28'),
            (6, 'sq2', 'SQ2', 1, 6, '2018-05-15 22:10:45', '2018-05-15 22:10:45'),
            (7, 'srv', 'SRV', 1, 7, '2018-05-15 22:11:01', '2018-05-15 22:11:01'),
            (8, 'srv2', 'SRV2', 1, 8, '2018-05-15 22:11:27', '2018-05-15 22:11:27'),
            (9, 'srvby', 'SRVBY', 1, 9, '2018-05-15 22:13:09', '2018-05-15 22:14:07'),
            (10, 'ss2trk', 'SS2TRK', 1, 10, '2018-05-15 22:13:28', '2018-05-15 22:13:28');
        ";

        DB::statement($sql);
    }
}
