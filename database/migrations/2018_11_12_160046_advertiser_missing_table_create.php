<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdvertiserMissingTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertiser_missing', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 255)->collation('utf8mb4_unicode_ci');
            $table->string('contact', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('email', 255)->collation('utf8mb4_unicode_ci')->nullable();

            $table->string('country', 7)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('state', 7)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('city', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('street1', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('zip', 63)->collation('utf8mb4_unicode_ci')->nullable();

            $table->integer('currency_id')->nullable()->default(0);

            $table->integer('manager_id')->nullable();
            $table->integer('manager_account_id')->nullable()->default(0);

            $table->bigInteger('lt_id')->nullable()->default(0);
            $table->bigInteger('ef_id')->nullable()->default(0);
            $table->string('ef_status', 31)->collation('utf8mb4_unicode_ci')->nullable();

            $table->tinyInteger('status')->nullable()->default(0);
            $table->tinyInteger('is_duplicate')->nullable()->default(0);
            $table->integer('updated_by_id')->nullable()->default(0);

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
        Schema::dropIfExists('advertiser_missing');
    }
}
