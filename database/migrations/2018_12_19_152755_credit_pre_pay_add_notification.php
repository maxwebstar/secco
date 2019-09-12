<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreditPrePayAddNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_cap', function (Blueprint $table) {
            $table->tinyInteger('notify_limit')->nullable()->default(0)->after('num_month');
        });
        Schema::table('pre_pay', function (Blueprint $table) {
            $table->tinyInteger('notify_limit')->nullable()->default(0)->after('type');
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
            $table->dropColumn(['notify_limit']);
        });
        Schema::table('pre_pay', function (Blueprint $table) {
            $table->dropColumn(['notify_limit']);
        });
    }
}
