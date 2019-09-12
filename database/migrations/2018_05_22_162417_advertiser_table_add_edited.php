<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdvertiserTableAddEdited extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('advertiser', function (Blueprint $table) {

            $table->increments('id')->first();
            $table->string('edited_by', 255)->collation('utf8mb4_unicode_ci')->nullable()->after('ef_id');
            $table->dateTime('edited_at')->nullable()->after('edited_by');
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
            $table->dropColumn(['edited_by', 'edited_at']);
        });
    }
}
