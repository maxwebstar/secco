<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OfferAffiliateCreateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_affiliate', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('network_id');
            $table->integer('offer_id');
            $table->integer('affiliate_id');
            $table->bigInteger('network_offer_id');
            $table->bigInteger('network_affiliate_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offer_affiliate');
    }
}
