<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AccessTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('label', 100)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('value', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->tinyInteger('position')->nullable()->default(0);
            $table->tinyInteger('show')->nullable()->default(0);
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
        Schema::dropIfExists('access');
    }
}
