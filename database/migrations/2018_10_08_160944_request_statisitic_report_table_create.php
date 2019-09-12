<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RequestStatisiticReportTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_statistic_report', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('advertiser_id');
            $table->integer('from_user_id')->nullable()->default(0);
            $table->text('subject')->collation('utf8mb4_unicode_ci');
            $table->text('body')->collation('utf8mb4_unicode_ci');
            $table->date('date')->nullable();
            $table->string('error', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->tinyInteger('status')->nullable()->default(3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_statistic_report');
    }
}
