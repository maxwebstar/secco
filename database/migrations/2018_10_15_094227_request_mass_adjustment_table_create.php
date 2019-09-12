<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RequestMassAdjustmentTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_mass_adjustment', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('network_id');
            $table->integer('offer_id');
            $table->integer('affiliate_id')->nullable()->default(0);

            $table->integer('click')->nullable()->default(0);
            $table->integer('qualified')->nullable()->default(0);
            $table->integer('approved')->nullable()->default(0);
            $table->float('revenue')->nullable()->default(0);
            $table->float('commission')->nullable()->default(0);

            $table->date('date');
            $table->tinyInteger('type');
            $table->text('reason')->collation('utf8mb4_unicode_ci')->nullable();
            $table->tinyInteger('status')->nullable()->default(0);
            $table->string('created_by', 63)->collation('utf8mb4_unicode_ci');
            $table->integer('created_by_id');
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
        Schema::dropIfExists('request_mass_adjustment');
    }
}
