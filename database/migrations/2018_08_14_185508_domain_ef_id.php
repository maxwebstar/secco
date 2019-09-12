<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DomainEfId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domain', function (Blueprint $table) {
            $table->bigInteger('ef_id')->nullable()->default(0)->after('id');
            $table->tinyInteger('is_lt')->nullable()->default(0)->after('position');
            $table->tinyInteger('is_ef')->nullable()->default(0)->after('is_lt');
            $table->string('value', 63)->collation('utf8mb4_unicode_ci')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('domain', function (Blueprint $table) {
            $table->dropColumn(['ef_id', 'is_lt', 'is_ef']);
        });
    }
}
