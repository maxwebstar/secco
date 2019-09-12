<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FrequencyDelete extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('io', function (Blueprint $table) {
            $table->dropColumn(['payment_frequency']);
        });

        Schema::table('advertiser', function (Blueprint $table) {
            $table->dropColumn(['payment_frequency']);
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
            $table->string('payment_frequency', 63)->collation('utf8mb4_unicode_ci')->nullable()->after('time');
        });

        Schema::table('advertiser', function (Blueprint $table) {
            $table->string('payment_frequency', 63)->collation('utf8mb4_unicode_ci')->nullable()->after('cap');
        });
    }
}
