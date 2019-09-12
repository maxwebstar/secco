<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreditCapQbTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qb_advertiser_report', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('advertiser_id');
            $table->float('amount')->nullable()->default(0);
            $table->tinyInteger('type')->nullable()->default(0);
            $table->date('date')->nullable();
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
        Schema::dropIfExists('qb_advertiser_report');
    }
}
