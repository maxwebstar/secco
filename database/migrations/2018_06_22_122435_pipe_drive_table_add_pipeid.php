<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PipeDriveTableAddPipeid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pipe_drive_deal', function (Blueprint $table) {
            $table->integer('pd_deal_id')->nullable()->default(0)->after('id');
            $table->integer('pd_organization_id')->nullable()->default(0)->after('pd_deal_id');
            $table->integer('pd_person_id')->nullable()->default(0)->after('pd_organization_id');
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
            $table->dropColumn(['pd_deal_id', 'pd_organization_id', 'pd_person_id']);
        });
    }
}
