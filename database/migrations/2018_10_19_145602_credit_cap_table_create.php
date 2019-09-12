<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreditCapTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_cap', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('advertiser_id');

            $table->double('revenue', 12, 2)->nullable()->default(0);
            $table->double('balance', 12, 2)->nullable()->default(0);
            $table->double('cap', 12, 2)->nullable()->default(0);
            $table->float('cap_percent')->nullable()->default(0);

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
        Schema::dropIfExists('credit_cap');
    }
}
