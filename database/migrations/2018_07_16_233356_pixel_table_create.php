<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PixelTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pixel', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key', 15)->collation('utf8mb4_unicode_ci');
            $table->string('name', 63)->collation('utf8mb4_unicode_ci');
            $table->tinyInteger('position')->nullable()->default(0);
            $table->tinyInteger('show')->nullable()->default(1);
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
        Schema::dropIfExists('pixel');
    }
}
