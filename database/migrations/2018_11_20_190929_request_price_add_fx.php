<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RequestPriceAddFx extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_price', function (Blueprint $table) {
            $table->tinyInteger('is_fx_rate')->nullable()->default(0)->after('status');
            $table->text('error_api')->collation('utf8mb4_unicode_ci')->nullable()->after('ef_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_price', function (Blueprint $table) {
            $table->dropColumn(['is_fx_rate', 'error_api']);
        });
    }
}
