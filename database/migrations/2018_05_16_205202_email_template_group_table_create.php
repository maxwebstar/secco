<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EmailTemplateGroupTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_template_group', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->collation('utf8mb4_unicode_ci');
            $table->string('display_name', 255)->collation('utf8mb4_unicode_ci');
            $table->tinyInteger('show')->nullable()->default(1);
            $table->tinyInteger('position')->nullable()->default(0);
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
        Schema::dropIfExists('email_template_group');
    }
}
