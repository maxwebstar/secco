<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OfferCreativeChangeKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offer_creative', function (Blueprint $table) {
            $table->dropIndex('offer_creative_offer_id_iteration_unique');
            $table->unique(['offer_id', 'request_id', 'iteration']);
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
            //
        });
    }
}
