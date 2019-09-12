<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdvertiserTableAddMongo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('advertiser', function (Blueprint $table) {
            $table->string('mongo_id', 63)->collation('utf8mb4_unicode_ci')->nullable()->after('updated_at');
            $table->string('billing_email', 255)->collation('utf8mb4_unicode_ci')->nullable()->after('email');
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
            $table->dropColumn(['mongo_id']);
        });
    }
}
