<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreditCapAddMonthNum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_cap', function (Blueprint $table) {
            $table->tinyInteger('num_month')->nullable()->default(0)->after('is_6_month');
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
            $table->dropColumn(['num_month']);
        });
    }
}
