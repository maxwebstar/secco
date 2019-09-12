<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IoCreditFile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('io', function (Blueprint $table) {
            $table->string('credit_local_file', 255)->collation('utf8mb4_unicode_ci')->nullable()->after('docusign_google_url');
        });

        Schema::table('pixel', function (Blueprint $table) {
            $table->string('ef_key', 31)->collation('utf8mb4_unicode_ci')->nullable()->change();
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
            $table->dropColumn(['credit_local_file']);
        });
    }
}
