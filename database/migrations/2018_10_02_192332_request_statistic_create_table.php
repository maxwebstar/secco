<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RequestStatisticCreateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_statistic', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('advertiser_id');
            $table->integer('manager_account_id')->nullable()->default(0);
            $table->string('advertiser_contact', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('advertiser_email', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->tinyInteger('notification')->nullable()->default(0);
            $table->text('reason')->collation('utf8mb4_unicode_ci')->nullable();
            $table->integer('updated_by_id')->nullable()->default(0);
            $table->integer('created_by_id');
            $table->timestamps();

            $table->unique('advertiser_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_statistic');
    }
}
