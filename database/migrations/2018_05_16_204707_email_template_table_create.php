<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EmailTemplateTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_template', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('group_id');
            $table->string('name', 63)->collation('utf8mb4_unicode_ci');
            $table->string('display_name', 255)->collation('utf8mb4_unicode_ci');
            $table->text('to')->nullable()->collation('utf8mb4_unicode_ci');
            $table->string('from_name', 255)->nullable()->collation('utf8mb4_unicode_ci');
            $table->string('from_email', 255)->nullable()->collation('utf8mb4_unicode_ci');
            $table->string('subject', 255)->collation('utf8mb4_unicode_ci');
            $table->text('body')->collation('utf8mb4_unicode_ci');
            $table->tinyInteger('status')->nullable()->default(3);
            $table->tinyInteger('position')->nullable()->default(0);
            $table->text('description')->nullable()->collation('utf8mb4_unicode_ci');
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
        Schema::dropIfExists('email_template');
    }
}
