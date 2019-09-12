<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RequestCreativeTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_creative', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('offer_id')->nullable()->default(0);
            $table->tinyInteger('need_api_lt')->nullable()->default(0);
            $table->tinyInteger('need_api_ef')->nullable()->default(0);

            $table->string('type_traffic', 100)->collation('utf8mb4_unicode_ci')->nullable();
            $table->text('restrictions')->collation('utf8mb4_unicode_ci')->nullable();
            $table->text('demos')->collation('utf8mb4_unicode_ci')->nullable();
            $table->text('notes')->collation('utf8mb4_unicode_ci')->nullable();

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
        Schema::dropIfExists('request_creative');
    }
}
