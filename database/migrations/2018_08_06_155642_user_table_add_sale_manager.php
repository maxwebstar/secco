<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserTableAddSaleManager extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('show_for_sale_manage_list')->nullable()->default(0)->after('show_for_manage_list');

        });

        Schema::table('advertiser', function (Blueprint $table) {
            $table->integer('manager_sale_id')->nullable()->default(0)->after('manager_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['show_for_sale_manage_list']);
        });

        Schema::table('advertiser', function (Blueprint $table) {
            $table->dropColumn(['manager_sale_id']);
        });
    }
}
