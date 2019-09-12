<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IoTableChangeCreatedBy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('io', function (Blueprint $table) {
            $table->string('created_by', 63)->collation('utf8mb4_unicode_ci')->nullable()->change();
            $table->integer('created_by_id')->nullable()->default(0)->change();
            $table->string('google_folder', 255)->collation('utf8mb4_unicode_ci')->nullable()->change();
            $table->string('google_file', 255)->collation('utf8mb4_unicode_ci')->nullable()->change();
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
            //
        });
    }
}
