<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RequestCapTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_cap', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('offer_id')->nullable()->default(0);
            $table->date('date')->nullable();
            $table->integer('cap')->nullable()->default(0);
            $table->tinyInteger('cap_type_id')->nullable()->default(0);
            $table->tinyInteger('cap_reset')->nullable()->default(0);
            $table->text('redirect_url')->collation('utf8mb4_unicode_ci')->nullable();
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
        Schema::dropIfExists('request_cap');
    }
}
