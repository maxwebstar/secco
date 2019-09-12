<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PrePayTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_pay', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('advertiser_id')->nullable()->default(0);
            $table->double('amount', 12, 2)->nullable()->default(0);
            $table->double('revenue_mtd', 12, 2)->nullable()->default(0);
            $table->double('balance_remaining', 12, 2)->nullable()->default(0);
            $table->float('used_percent')->nullable()->default(0);
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
        Schema::dropIfExists('pre_pay');
    }
}
