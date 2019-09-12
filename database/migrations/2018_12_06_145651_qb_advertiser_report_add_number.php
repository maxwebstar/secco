<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class QbAdvertiserReportAddNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('qb_advertiser_report', function (Blueprint $table) {
            $table->integer('qb_number')->nullable()->default(0)->after('quickbook_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('qb_advertiser_report', function (Blueprint $table) {
            $table->dropColumn(['qb_number']);
        });
    }
}
