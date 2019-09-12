<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OfferCreativeTableAddMongo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offer_creative', function (Blueprint $table) {
            $table->string('mongo_offer_id', 63)->collation('utf8mb4_unicode_ci')->nullable()->after('updated_at');
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
            $table->dropColumn(['mongo_campaign_id']);
        });
    }
}
