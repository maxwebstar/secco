<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OfferCreativeMissingAddEfStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offer_creative_missing', function (Blueprint $table) {
            $table->string('ef_status', 31)->nullable()->collation('utf8mb4_unicode_ci')->after('ef_id');
            $table->integer('creative_id')->nullable()->default(0)->after('offer_id');

            $table->integer('updated_by_id')->nullable()->default(0)->after('status');
            $table->string('updated_by', 255)->nullable()->collation('utf8mb4_unicode_ci')->after('updated_by_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offer_creative_missing', function (Blueprint $table) {
            $table->dropColumn(['ef_status', 'creative_id', 'updated_by_id', 'updated_by']);
        });
    }
}
