<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RequestStatisitcTableFromUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_statistic', function (Blueprint $table) {
            $table->integer('from_user_id')->nullable()->default(0)->after('advertiser_id');

            $table->dropColumn(['manager_account_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_statistic', function (Blueprint $table) {
            //
        });
    }
}
