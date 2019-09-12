<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdvertiserTableAddCountryStateId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('advertiser', function (Blueprint $table) {
//            $table->integer('country_id')->after('country');
//            $table->integer('state_id')->nullable()->after('state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('advertiser', function (Blueprint $table) {
//            $table->dropColumn(['country_id', 'state_id']);
        });
    }
}
