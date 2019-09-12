<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OfferReportAddApproved extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offer_report', function (Blueprint $table) {
            $table->integer('approved')->nullable()->default(0)->after('unique_click');
            $table->double('payout', 8, 2)->nullable()->default(0)->after('revenue');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offer_report', function (Blueprint $table) {
            $table->dropColumn(['approved', 'payout']);
        });
    }
}
