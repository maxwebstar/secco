<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class QbAddIdToAdvertAndReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('qb_advertiser_report', function (Blueprint $table) {
            $table->integer('quickbook_id')->nullable()->default(0)->after('advertiser_id');
            $table->integer('currency_id')->nullable()->default(0)->after('quickbook_id');
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
            $table->dropColumn(['quickbook_id', 'currency_id']);
        });
    }
}
