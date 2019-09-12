<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RequestCapAddCronError extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_cap', function (Blueprint $table) {
            $table->text('error_cron')->collation('utf8mb4_unicode_ci')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_cap', function (Blueprint $table) {
            $table->dropColumn(['error_cron']);
        });
    }
}
