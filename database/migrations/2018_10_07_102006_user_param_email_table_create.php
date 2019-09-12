<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserParamEmailTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_param_email', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id');
            $table->string('driver', 63)->collation('utf8mb4_unicode_ci');
            $table->string('host', 63)->collation('utf8mb4_unicode_ci');
            $table->string('port', 63)->collation('utf8mb4_unicode_ci');
            $table->string('username', 255)->collation('utf8mb4_unicode_ci');
            $table->string('password', 255)->collation('utf8mb4_unicode_ci');
            $table->string('encryption', 63)->collation('utf8mb4_unicode_ci');

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
        Schema::dropIfExists('user_param_email');
    }
}
