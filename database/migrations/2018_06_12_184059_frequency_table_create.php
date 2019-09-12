<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FrequencyTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('frequency', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 63)->collation('utf8mb4_unicode_ci');
            $table->string('lt_name', 63)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('ef_name', 63)->collation('utf8mb4_unicode_ci')->nullable();
            $table->tinyInteger('position')->nullable()->default(0);
            $table->tinyInteger('show')->nullable()->default(1);

            $table->timestamps();
        });

        Schema::table('io', function (Blueprint $table) {
            $table->integer('frequency_id')->nullable()->default(0)->after('payment_frequency');
        });
        Schema::table('advertiser', function (Blueprint $table) {
            $table->integer('frequency_id')->nullable()->default(0)->after('payment_frequency');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('frequency');

        Schema::table('io', function (Blueprint $table) {
            $table->dropColumn(['frequency_id']);
        });
        Schema::table('advertiser', function (Blueprint $table) {
            $table->dropColumn(['frequency_id']);
        });
    }
}
