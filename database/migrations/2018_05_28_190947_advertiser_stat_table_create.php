<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdvertiserStatTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertiser_stat', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('advertiser_id');
            $table->integer('lt_id')->nullable()->default(0);
            $table->integer('ef_id')->nullable()->default(0);
            $table->tinyInteger('network_id');
            $table->date('date');
            $table->integer('approved')->nullable()->default(0);
            $table->integer('click');
            $table->float('revenue');
            $table->float('payout');
            $table->float('profit');
            $table->timestamps();

            $table->unique(['advertiser_id', 'network_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advertiser_stat');
    }
}
