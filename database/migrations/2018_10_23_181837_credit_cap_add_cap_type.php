<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreditCapAddCapType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_cap', function (Blueprint $table) {
            $table->tinyInteger('cap_type')->nullable()->default(0)->after('cap');
            $table->tinyInteger('is_6_month')->nullable()->default(0)->after('cap_type');
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
            $table->dropColumn(['cap_type', 'is_6_month']);
        });
    }
}
