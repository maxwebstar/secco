<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ImportErrorAdvertiserTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_error_advertiser', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mongo_id', 63)->collation('utf8mb4_unicode_ci');
            $table->text('error')->collation('utf8mb4_unicode_ci')->nullable();;
            $table->timestamps();

            $table->unique('mongo_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('import_error_advertiser');
    }
}
