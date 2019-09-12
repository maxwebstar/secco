<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OfferCategoryEfId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offer_category', function (Blueprint $table) {
            $table->bigInteger('ef_id')->nullable()->default(0)->after('id');
            $table->tinyInteger('is_lt')->nullable()->default(0)->after('show');
            $table->tinyInteger('is_ef')->nullable()->default(0)->after('is_lt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offer_category', function (Blueprint $table) {
            $table->dropColumn(['ef_id', 'is_lt', 'is_ef']);
        });
    }
}
