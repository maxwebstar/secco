<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NetworkTableAddPositionDefault extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('network', function (Blueprint $table) {
            $table->string('field_name', 63)->collation('utf8mb4_unicode_ci')->after('display_name');
            $table->tinyInteger('position')->nullable()->default(0)->after('field_name');
            $table->tinyInteger('by_default')->nullable()->default(0)->after('position');
            $table->tinyInteger('show')->nullable()->default(1)->after('by_default');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('network', function (Blueprint $table) {
            $table->dropColumn(['field_name', 'position', 'by_default', 'show']);
        });
    }
}
