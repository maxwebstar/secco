<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OfferTableAddSaleManager extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offer', function (Blueprint $table) {
            $table->integer('manager_sale_id')->nullable()->default(0)->after('manager_id');
            $table->tinyInteger('need_api_lt')->nullable()->default(0)->after('internal_note');
            $table->tinyInteger('need_api_ef')->nullable()->default(0)->after('need_api_lt');
            $table->string('ef_status', 31)->collation('utf8mb4_unicode_ci')->nullable()->after('ef_id');
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
            $table->dropColumn(['manager_sale_id', 'need_api_lt', 'need_api_ef', 'ef_status']);
        });
    }
}
