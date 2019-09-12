<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class QbAccessTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qb_access', function (Blueprint $table) {
            $table->increments('id');
            $table->string('real_m_id', 255)->collation('utf8mb4_unicode_ci');
            $table->text('access_token')->collation('utf8mb4_unicode_ci');
            $table->text('refresh_token')->collation('utf8mb4_unicode_ci')->nullable();
            $table->integer('expires_in')->nullable();
            $table->integer('refresh_token_expires_in')->nullable();
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
        Schema::dropIfExists('qb_access');
    }
}
