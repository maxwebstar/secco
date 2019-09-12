<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdvertiserTableAddPipedrive extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('advertiser', function (Blueprint $table) {
            $table->integer('pipedrive_id')->nullable()->default(0)->after('ef_status');
        });
        Schema::table('io', function (Blueprint $table) {
            $table->integer('pipedrive_id')->nullable()->default(0)->after('frequency_custom');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('advertiser', function (Blueprint $table) {
            $table->dropColumn(['pipedrive_id']);
        });

        Schema::table('io', function (Blueprint $table) {
            $table->dropColumn(['pipedrive_id']);
        });
    }
}
