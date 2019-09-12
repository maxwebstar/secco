<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TmpEfOfferCreateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmp_ef_offer', function (Blueprint $table) {
            $table->integer('network_offer_id');
            $table->integer('network_id');
            $table->integer('network_advertiser_id');
            $table->integer('network_offer_group_id');
            $table->string('name', 255)->collation('utf8mb4_unicode_ci');
            $table->text('thumbnail_url')->collation('utf8mb4_unicode_ci')->nullable();
            $table->integer('network_category_id');
            $table->text('internal_notes')->collation('utf8mb4_unicode_ci')->nullable();
            $table->text('destination_url')->collation('utf8mb4_unicode_ci');
            $table->text('server_side_url')->collation('utf8mb4_unicode_ci')->nullable();
            $table->boolean('is_view_through_enabled');
            $table->text('view_through_destination_url')->collation('utf8mb4_unicode_ci')->nullable();
            $table->text('preview_url')->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('offer_status', 15)->collation('utf8mb4_unicode_ci');
            $table->string('currency_id', 7)->collation('utf8mb4_unicode_ci');
            $table->string('project_id', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->json('relationship')->nullable();
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
        Schema::dropIfExists('tmp_ef_offer');
    }
}
