<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TermTemplateGroupTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('term_template_group', function (Blueprint $table) {
            $table->increments('id');
            $table->string('display_name', 255)->collation('utf8mb4_unicode_ci');
            $table->tinyInteger('position')->nullable()->default(0);
            $table->tinyInteger('by_default')->nullable()->default(0);
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
        Schema::dropIfExists('term_template_group');
    }
}
