<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OfferUrlTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_url', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('offer_id');
            $table->string('name', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->text('url')->collation('utf8mb4_unicode_ci');
            $table->bigInteger('ef_id')->nullable()->default(0);
            $table->string('ef_status', 31)->collation('utf8mb4_unicode_ci')->nullable();
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
        Schema::dropIfExists('offer_url');
    }
}
