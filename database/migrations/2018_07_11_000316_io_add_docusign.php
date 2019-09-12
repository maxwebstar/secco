<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IoAddDocusign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('io', function (Blueprint $table) {
            $table->string('docusign_email_advertiser', 63)->collation('utf8mb4_unicode_ci')->nullable()->after('pipedrive_id');
            $table->string('docusign_name_advertiser', 63)->collation('utf8mb4_unicode_ci')->nullable()->after('docusign_email_advertiser');
            $table->integer('docusign_manager_id')->nullable()->default(0)->after('docusign_name_advertiser');
            $table->string('docusign_id', 100)->collation('utf8mb4_unicode_ci')->nullable()->after('docusign_manager_id');
            $table->string('docusign_file', 255)->collation('utf8mb4_unicode_ci')->nullable()->after('docusign_id');
            $table->tinyInteger('file_pdf_exist')->nullable()->default(0)->after('google_file_name');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('docusign_manager')->nullable()->default(0)->after('show_for_manage_list');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('io', function (Blueprint $table) {
            $table->dropColumn(['docusign_email', 'docusign_name', 'docusign_manager_id', 'docusign_id', 'docusign_file', 'file_pdf_exist']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['docusign_manager']);
        });
    }
}
