<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CampaignTypeAddEfKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_type', function (Blueprint $table) {
            $table->string('ef_key', 15)->collation('utf8mb4_unicode_ci')->nullable()->after('key');
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
        Schema::table('campaign_type', function (Blueprint $table) {
            $table->dropColumn(['ef_key', 'is_lt', 'is_ef']);
        });
    }
}
