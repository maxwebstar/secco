<?php

use Illuminate\Database\Seeder;

class IOTemplateDocNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql1 = "UPDATE `io_template_doc` SET `file_name` = 'IOTemplate.docx' WHERE id = 1;";
        $sql2 = "UPDATE `io_template_doc` SET `file_name` = 'IOTemplate_n30.docx' WHERE id = 2;";
        $sql3 = "UPDATE `io_template_doc` SET `file_name` = 'IOTemplate_gov.docx' WHERE id = 3;";
        $sql4 = "UPDATE `io_template_doc` SET `file_name` = 'IOTemplate_orig.docx' WHERE id = 4;";

        DB::statement($sql1);
        DB::statement($sql2);
        DB::statement($sql3);
        DB::statement($sql4);
    }
}
