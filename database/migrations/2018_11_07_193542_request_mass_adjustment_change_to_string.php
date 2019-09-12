<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RequestMassAdjustmentChangeToString extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_mass_adjustment', function (Blueprint $table) {
            $table->string('offer_id', 12)->change();
            $table->string('affiliate_id', 12)->nullable()->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_mass_adjustment', function (Blueprint $table) {
            $table->integer('offer_id')->change();
            $table->integer('affiliate_id')->change();
        });
    }
}
