<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class QbCustomerTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qb_customer', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('advertiser_id')->nullable()->default(0);
            $table->integer('advertiser_network_id')->nullable()->default(0);
            $table->integer('quickbook_id')->nullable()->default(0);
            $table->string('name', 255)->collation('utf8mb4_unicode_ci');
            $table->string('email', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('phone', 63)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('company', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->tinyInteger('active')->nullable()->default(0);
            $table->dateTime('created_qb');
            $table->tinyInteger('status')->nullable()->default(0);
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
        Schema::table('qb_customer', function (Blueprint $table) {
            Schema::dropIfExists('qb_customer');
        });
    }
}
