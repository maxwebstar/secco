<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreditCapChangeDouble extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_cap', function (Blueprint $table) {
//            $table->double('revenue_new', 12, 2)->nullable()->default(0);
//            $table->double('balance_new', 12, 2)->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::table('creadit_cap', function (Blueprint $table) {
//            //
//        });
    }
}
