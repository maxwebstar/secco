<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class QbReportAddOpenAmount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_cap', function (Blueprint $table) {
            $table->double('ar', 12, 2)->nullable()->default(0)->after('cap_type');
            $table->double('revenue_mtd', 12, 2)->nullable()->default(0)->after('revenue');
        });

        Schema::table('pre_pay', function (Blueprint $table) {
            $table->double('ar', 12, 2)->nullable()->default(0)->after('revenue_mtd');
            $table->double('revenue', 12, 2)->nullable()->default(0)->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('credit_cap', function (Blueprint $table) {
            $table->dropColumn(['ar', 'revenue_mtd']);
        });

        Schema::table('pre_pay', function (Blueprint $table) {
            $table->dropColumn(['ar', 'revenue']);
        });
    }
}
