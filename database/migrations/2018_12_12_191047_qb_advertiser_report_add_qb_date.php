<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class QbAdvertiserReportAddQbDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('qb_advertiser_report', function (Blueprint $table) {
            $table->dateTime('created_qb')->nullable()->after('date');
            $table->dateTime('updated_qb')->nullable()->after('created_qb');
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
            $table->dropColumn(['created_qb']);
            $table->dropColumn(['updated_qb']);
        });
    }
}
