<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RequestCreativeAddCapCapType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_creative', function (Blueprint $table) {
            $table->integer('cap')->nullable()->default(0)->after('need_api_ef');
            $table->tinyInteger('cap_type_id')->nullable()->default(0)->after('cap');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_creative', function (Blueprint $table) {
            $table->dropColumn(['cap', 'cap_type_id']);
        });
    }
}
