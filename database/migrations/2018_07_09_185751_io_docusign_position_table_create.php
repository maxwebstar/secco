<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IoDocusignPositionTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('io_docusign_position', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 63)->collation('utf8mb4_unicode_ci');

            $table->string('secco_string', 255)->collation('utf8mb4_unicode_ci');
            $table->string('secco_units', 63)->collation('utf8mb4_unicode_ci');
            $table->integer('secco_x_offset');
            $table->integer('secco_y_offset');

            $table->string('client_string', 255)->collation('utf8mb4_unicode_ci');
            $table->string('client_units', 63)->collation('utf8mb4_unicode_ci');
            $table->integer('client_x_offset');
            $table->integer('client_y_offset');

            $table->string('type', 63)->collation('utf8mb4_unicode_ci');
            $table->tinyInteger('template_id');
            $table->integer('position');
            $table->timestamps();

            $table->unique(['template_id', 'type']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('io_docusign_position');
    }
}
