<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OfferCreativeAddEfStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offer_creative', function (Blueprint $table) {
            $table->string('ef_status', 31)->nullable()->collation('utf8mb4_unicode_ci')->after('ef_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offer_creative', function (Blueprint $table) {
            $table->dropColumn(['ef_status']);
        });
    }
}
