<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PipeDriveDealTableChangeName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pipe_drive_deal', function (Blueprint $table) {
            $table->integer('pd_user_id')->nullable()->default(0)->after('pd_person_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pipe_drive_deal', function (Blueprint $table) {
            $table->dropColumn(['pd_user_id']);
        });
    }
}
