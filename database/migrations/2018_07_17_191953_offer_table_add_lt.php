<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OfferTableAddLt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offer', function (Blueprint $table) {
            $table->bigInteger('lt_id')->nullable()->default(0)->after('internal_note');
            $table->bigInteger('ef_id')->nullable()->default(0)->after('lt_id');
            $table->string('mongo_campaign_id', 63)->collation('utf8mb4_unicode_ci')->nullable()->after('updated_at');
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
            $table->dropColumn(['lt_id', 'ef_id','mongo_campaign_id']);
        });
    }
}
