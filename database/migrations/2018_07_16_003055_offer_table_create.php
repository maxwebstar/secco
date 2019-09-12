<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OfferTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer', function (Blueprint $table) {
            $table->increments('id');

            $table->string('campaign_name', 255)->collation('utf8mb4_unicode_ci');           /*campname*/
            $table->string('campaign_type', 7)->collation('utf8mb4_unicode_ci')->nullable(); /*campaignType*/
            $table->text('campaign_link')->collation('utf8mb4_unicode_ci');                  /*link*/
            $table->integer('manager_id')->nullable()->default(0);
            $table->integer('advertiser_id')->nullable()->default(0);
            $table->string('advertiser_contact', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('advertiser_email', 255)->collation('utf8mb4_unicode_ci')->nullable();

            $table->integer('offer_category_id')->nullable()->default(0); /*offerType*/
            $table->integer('domain_id')->nullable()->default(0);         /*domain*/

            $table->integer('pixel_id')->nullable()->default(0); /*pixelType*/
            $table->text('pixel_location')->collation('utf8mb4_unicode_ci')->nullable();

            $table->tinyInteger('redirect')->nullable()->default(0); /*redirect [Y, N]*/
            $table->text('redirect_url')->collation('utf8mb4_unicode_ci')->nullable();

            $table->tinyInteger('cap_type_id')->nullable()->default(0); /*capType*/
            $table->tinyInteger('cap_unit_id')->nullable()->default(0); /*leadCapType*/

            $table->string('cap_monetary', 31)->collation('utf8mb4_unicode_ci')->nullable(); /*monetarycap $125, 50.00, 1,000 (validator = integer)*/
            $table->string('cap_lead', 31)->collation('utf8mb4_unicode_ci')->nullable();     /*leadcap NaN, uncapped, Infinity, 250 (validator = integer)*/

            $table->string('price_in', 31)->collation('utf8mb4_unicode_ci')->nullable();  /*newin 70$, 50.00 (validator = integer)*/
            $table->string('price_out', 31)->collation('utf8mb4_unicode_ci')->nullable(); /*newout 12$ 62.50, €0.95 (validator = integer)*/

            $table->string('geos', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('geo_redirect_url', 255)->collation('utf8mb4_unicode_ci')->nullable();

            $table->longText('accepted_traffic')->collation('utf8mb4_unicode_ci')->nullable(); /*acceptedTraffic*/
            $table->longText('affiliate_note')->collation('utf8mb4_unicode_ci')->nullable();   /*affiliatesNotes*/
            $table->longText('internal_note')->collation('utf8mb4_unicode_ci')->nullable();    /*internalNotes*/

            $table->tinyInteger('status')->nullable()->default(0); /*approvalStatus [Approved, Declined, pending]*/

            $table->string('created_by', 63)->collation('utf8mb4_unicode_ci');
            $table->integer('created_by_id');
            $table->timestamps();
            $table->string('mongo_user_id', 63)->nullable();
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
        Schema::dropIfExists('offer');
    }
}
