<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ImportAdditionAdvertiserCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_addition_advertiser', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('lt_id')->nullable()->default(0);
            $table->string('mongo_id', 63)->collation('utf8mb4_unicode_ci')->nullable();
            $table->dateTime('time')->nullable();
            $table->string('statReqUser', 63)->collation('utf8mb4_unicode_ci')->nullable();
            $table->dateTime('edateTime')->nullable();
            $table->string('frequency', 63)->collation('utf8mb4_unicode_ci')->nullable();
            $table->dateTime('lastSent')->nullable();
            $table->boolean('statRequest')->nullable();
            $table->dateTime('nextRecurring')->nullable();
            $table->boolean('sent')->nullable();
            $table->boolean('recurring')->nullable();

            $table->unique('mongo_id');
            $table->unique('lt_id');

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
        Schema::dropIfExists('import_addition_advertiser');
    }
}
