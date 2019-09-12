<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PermissionsGroupCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions_group', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 63);
            $table->string('display_name', 63);
            $table->string('description', 255)->nullable()->default('');
            $table->integer('position')->default(0);
            $table->tinyInteger('show')->default(1);
            $table->timestamps();

            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions_group');
    }
}
