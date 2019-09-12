<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class QbCustomerAddAr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('qb_customer', function (Blueprint $table) {
            $table->double('ar', 12, 2)->nullable()->default(0)->after('quickbook_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('qb_customer', function (Blueprint $table) {
            $table->dropColumn(['ar']);
        });
    }
}
