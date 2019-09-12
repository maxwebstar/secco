<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AffiliateTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliate', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->collation('utf8mb4_unicode_ci');

            $table->integer('manager_id')->nullable()->default(0);
            $table->string('email', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('contact', 255)->collation('utf8mb4_unicode_ci')->nullable();

            $table->integer('country_id')->nullable()->default(0);
            $table->integer('state_id')->nullable()->default(0);
            $table->string('city', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('street1', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('street2', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('zip', 63)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('phone', 63)->collation('utf8mb4_unicode_ci')->nullable();

            $table->string('im_network', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('im_address', 255)->collation('utf8mb4_unicode_ci')->nullable();

            $table->bigInteger('lt_id')->nullable()->default(0);
            $table->bigInteger('ef_id')->nullable()->default(0);

            $table->string('lt_status', 31)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('ef_status', 31)->collation('utf8mb4_unicode_ci')->nullable();

            $table->dateTime('last_login')->nullable();
            $table->string('updated_by', 63)->collation('utf8mb4_unicode_ci')->nullable();
            $table->integer('updated_by_id')->nullable()->default(0);

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
        Schema::dropIfExists('affiliate');
    }
}
