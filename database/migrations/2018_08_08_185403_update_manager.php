<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateManager extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('advertiser', function (Blueprint $table) {
            $table->renameColumn('manager_sale_id', 'manager_account_id');
        });

        Schema::table('offer', function (Blueprint $table) {
            $table->renameColumn('manager_sale_id', 'manager_account_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('show_for_sale_manage_list', 'show_for_account_manage_list');
        });

        Schema::table('io', function (Blueprint $table) {
            $table->renameColumn('manager_id', 'manager_account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('advertiser', function (Blueprint $table) {
            //
        });

        Schema::table('offer', function (Blueprint $table) {
            //
        });

        Schema::table('users', function (Blueprint $table) {
            //
        });

        Schema::table('io', function (Blueprint $table) {
            //
        });
    }
}
