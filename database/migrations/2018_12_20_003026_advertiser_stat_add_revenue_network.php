<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdvertiserStatAddRevenueNetwork extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('advertiser_stat', function (Blueprint $table) {
//            $table->double('revenue_lt', 12, 2)->nullable()->default(0)->after('click');
//            $table->double('revenue_ef', 12, 2)->nullable()->default(0)->after('revenue_lt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('advertiser_stat', function (Blueprint $table) {
//            $table->dropColumn(['revenue_lt', 'revenue_ef']);
        });
    }
}
