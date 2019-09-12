<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TrafficPccTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traffic_ppc', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->collation('utf8mb4_unicode_ci');
            $table->string('value', 255)->collation('utf8mb4_unicode_ci');
            $table->tinyInteger('show')->nullable()->default(1);
            $table->integer('position')->nullable()->default(0);
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
        Schema::dropIfExists('traffic_pcc');
    }
}
