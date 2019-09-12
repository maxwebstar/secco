<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OfferCreativeAddRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offer_creative', function (Blueprint $table) {
            $table->integer('request_id')->nullable()->default(0)->after('offer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offer_creative', function (Blueprint $table) {
            $table->dropColumn(['request_id']);
        });
    }
}
