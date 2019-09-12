<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NetworkTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('network', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('network_id');
            $table->string('short_name', 7)->collation('utf8mb4_unicode_ci');
            $table->string('display_name', 255)->collation('utf8mb4_unicode_ci');
            $table->timestamps();

            $table->unique('network_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('network');
    }
}
