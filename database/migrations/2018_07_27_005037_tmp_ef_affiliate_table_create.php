<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TmpEFAffiliateTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmp_ef_affiliate', function (Blueprint $table) {
            $table->integer('network_affiliate_id');
            $table->integer('network_id');
            $table->string('name', 255)->collation('utf8mb4_unicode_ci');
            $table->integer('network_employee_id');
            $table->boolean('has_notifications');
            $table->integer('network_traffic_source_id');
            $table->integer('account_executive_id');
            $table->integer('adress_id');
            $table->string('default_currency_id', 7)->collation('utf8mb4_unicode_ci')->nullable();
            $table->boolean('is_contact_address_enabled');
            $table->timestamp('time_created')->nullable();
            $table->timestamp('time_saved')->nullable();
            $table->json('relationship')->nullable();
            $table->timestamps();

            $table->unique('network_affiliate_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tmp_ef_affiliate');
    }
}
