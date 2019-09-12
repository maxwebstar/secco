<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OfferAddLivePrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offer', function (Blueprint $table) {
            $table->string('ef_price_in', 31)->collation('utf8mb4_unicode_ci')->nullable()->after('price_out');
            $table->string('ef_price_out', 31)->collation('utf8mb4_unicode_ci')->nullable()->after('ef_price_in');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offer', function (Blueprint $table) {
            $table->dropColumn(['ef_price_in', 'ef_price_in']);
        });
    }
}
