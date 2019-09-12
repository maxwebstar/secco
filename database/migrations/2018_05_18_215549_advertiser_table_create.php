<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdvertiserTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertiser', function (Blueprint $table) {
            $table->string('name', 255)->collation('utf8mb4_unicode_ci');
            $table->string('contact', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('email', 255)->collation('utf8mb4_unicode_ci');

            $table->string('country', 7)->collation('utf8mb4_unicode_ci');
            $table->string('state', 7)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('province', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('city', 255)->collation('utf8mb4_unicode_ci');
            $table->string('street1', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('street2', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('zip', 63)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('phone', 63)->collation('utf8mb4_unicode_ci')->nullable();

            $table->string('currency', 7)->collation('utf8mb4_unicode_ci');

            $table->tinyInteger('prepay')->nullable()->default(0);
            $table->integer('prepay_amount')->nullable()->default(0);

            $table->integer('cap')->nullable();
            $table->string('google_folder', 255)->collation('utf8mb4_unicode_ci')->nullable();

            $table->integer('manager_id');
            $table->string('quickbook_id', 63)->nullable();
            $table->bigInteger('lt_id')->nullable()->default(0);
            $table->bigInteger('ef_id')->nullable()->default(0);

            $table->string('created_by', 255)->collation('utf8mb4_unicode_ci');
            $table->timestamps();


            $table->unique('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*Schema::dropIfExists('advertiser');*/
    }
}
