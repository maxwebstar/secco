<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OfferCreativeMissingCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_creative_missing', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('offer_id')->nullable()->default(0);
            $table->string('name', 255)->collation('utf8mb4_unicode_ci');
            $table->text('link')->collation('utf8mb4_unicode_ci');
            $table->string('price_in', 31)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('price_out', 31)->collation('utf8mb4_unicode_ci')->nullable();
            $table->bigInteger('lt_id')->nullable()->default(0);
            $table->bigInteger('ef_id')->nullable()->default(0);
            $table->tinyInteger('status')->nullable()->default(0);
            $table->timestamps();

            //$table->unique(['offer_id', 'iteration']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offer_creative_missing');
    }
}
