<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CurrencyRemove extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('io', function (Blueprint $table) {
            $table->integer('currency_id')->nullable()->default(0)->after('currency');
            $table->dropColumn(['currency']);
        });

        Schema::table('advertiser', function (Blueprint $table) {
            $table->integer('currency_id')->nullable()->default(0)->after('currency');
            $table->dropColumn(['currency']);
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
            $table->string('currency', 7)->collation('utf8mb4_unicode_ci')->after('phone');
            $table->dropColumn(['currency_id']);
        });

        Schema::table('io', function (Blueprint $table) {
            $table->string('currency', 7)->collation('utf8mb4_unicode_ci')->nullable()->after('advertiser_id');
            $table->dropColumn(['currency_id']);
        });
    }
}
