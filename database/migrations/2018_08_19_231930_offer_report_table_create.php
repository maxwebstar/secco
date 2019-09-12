<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OfferReportTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_report', function (Blueprint $table) {
            $table->tinyInteger('network_id');
            $table->bigInteger('offer_network_id');
            $table->date('date');
            $table->bigInteger('imp')->nullable()->default(0);
            $table->bigInteger('total_click')->nullable()->default(0);
            $table->bigInteger('unique_click')->nullable()->default(0);
            $table->float('revenue')->nullable()->default(0);
            $table->float('profit')->nullable()->default(0);
            $table->float('margin')->nullable()->default(0);
            $table->integer('offer_id')->nullable()->default(0);
            $table->bigInteger('lt_id')->nullable()->default(0);
            $table->bigInteger('ef_id')->nullable()->default(0);
            $table->timestamps();

            $table->unique(['network_id', 'offer_network_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offer_report');
    }
}
