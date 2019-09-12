<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IoAddDocusignGoogle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('io', function (Blueprint $table) {
            $table->string('docusign_google_file', 255)->collation('utf8mb4_unicode_ci')->nullable()->after('docusign_file');
            $table->string('docusign_google_url', 255)->collation('utf8mb4_unicode_ci')->nullable()->after('docusign_google_file');
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
            $table->dropColumn(['docusign_google_file', 'docusign_google_url']);
        });
    }
}
