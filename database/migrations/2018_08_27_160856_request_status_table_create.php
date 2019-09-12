<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RequestStatusTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_status', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('offer_id')->nullable()->default(0);
            $table->date('date')->nullable();

            $table->tinyInteger('need_api_lt')->nullable()->default(0);
            $table->tinyInteger('need_api_ef')->nullable()->default(0);

            $table->string('lt_status', 31)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('ef_status', 31)->collation('utf8mb4_unicode_ci')->nullable();

            $table->tinyInteger('mass_notice')->nullable()->default(0);
            $table->text('redirect_url')->collation('utf8mb4_unicode_ci')->nullable();
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
        Schema::dropIfExists('request_status');
    }
}
