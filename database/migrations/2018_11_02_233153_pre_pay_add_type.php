<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PrePayAddType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pre_pay', function (Blueprint $table) {
            $table->tinyInteger('type')->nullable()->default(0)->after('used_percent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pre_pay', function (Blueprint $table) {
            $table->dropColumn(['type']);
        });
    }
}
