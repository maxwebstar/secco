<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IoTemplateDocTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('io_template_doc', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 63)->collation('utf8mb4_unicode_ci');
            $table->string('file_name', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->tinyInteger('position')->nullable()->default(0);
            $table->tinyInteger('by_default')->nullable()->default(0);
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
        Schema::dropIfExists('io_template_doc');
    }
}
