<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ManagerPriorityTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('manager_priority', function (Blueprint $table) {
//            $table->increments('id');
//            $table->string('name', 63)->collation('utf8mb4_unicode_ci');
//            $table->string('field_name', 255)->collation('utf8mb4_unicode_ci')->nullable();
//            $table->tinyInteger('show')->nullable()->default(1);
//            $table->tinyInteger('position')->nullable()->default(0);
//            $table->tinyInteger('by_default')->nullable()->default(0);
//            $table->timestamps();
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::dropIfExists('manager_priority');
    }
}
