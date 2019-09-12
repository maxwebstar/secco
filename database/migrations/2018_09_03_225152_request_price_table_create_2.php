<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RequestPriceTableCreate2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_price', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('network_id')->nullable()->default(0);
            $table->integer('offer_id')->nullable()->default(0);
            $table->tinyInteger('affiliate_all')->nullable()->default(0);
            $table->integer('affiliate_id')->nullable()->default(0);
            $table->date('date')->nullable();

            $table->string('price_in', 31)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('price_out', 31)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('current_price_in', 31)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('current_price_out', 31)->collation('utf8mb4_unicode_ci')->nullable();

            $table->tinyInteger('type')->nullable()->default(0);
            $table->tinyInteger('cap_change')->nullable()->default(0);
            $table->text('reason')->collation('utf8mb4_unicode_ci')->nullable();
            $table->tinyInteger('status')->nullable()->default(0);
            $table->string('created_by', 63)->collation('utf8mb4_unicode_ci');
            $table->integer('created_by_id');
            $table->timestamps();
            $table->string('mongo_id', 63)->collation('utf8mb4_unicode_ci')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_price');
    }
}
