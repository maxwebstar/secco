<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IoTableAddRestricted extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('io', function (Blueprint $table) {
            $table->tinyInteger('restricted_no_adult')->nullable()->default(0)->after('note');
            $table->tinyInteger('restricted_no_incent')->nullable()->default(0)->after('restricted_no_adult');
            $table->tinyInteger('restricted_no_rebrokering')->nullable()->default(0)->after('restricted_no_incent');
            $table->tinyInteger('restricted_no_affiliate_net')->nullable()->default(0)->after('restricted_no_rebrokering');
            $table->tinyInteger('restricted_none')->nullable()->default(0)->after('restricted_no_affiliate_net');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('io', function (Blueprint $table) {
            $table->dropColumn(['restricted_no_adult', 'restricted_no_incent', 'restricted_no_rebrokering', 'restricted_no_affiliate_net', 'restricted_none']);
        });
    }
}
