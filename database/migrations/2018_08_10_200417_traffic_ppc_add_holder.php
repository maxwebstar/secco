<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TrafficPpcAddHolder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('traffic_ppc', function (Blueprint $table) {
            $table->string('place_holder', 63)->collation('utf8mb4_unicode_ci')->nullable()->after('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('traffic_ppc', function (Blueprint $table) {
            $table->dropColumn(['place_holder']);
        });
    }
}
