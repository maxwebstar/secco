<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PipeDriveDealTableAddParam extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pipe_drive_deal', function (Blueprint $table) {
            $table->string('io_campaign_name', 255)->collation('utf8mb4_unicode_ci')->nullable()->after('id');
            $table->integer('currency_id')->nullable()->default(0)->after('io_campaign_name');

            $table->string('advertiser_name', 255)->collation('utf8mb4_unicode_ci')->nullable()->after('currency_id');
            $table->string('advertiser_contact', 255)->collation('utf8mb4_unicode_ci')->nullable()->after('advertiser_name');

            $table->string('advertiser_country', 7)->collation('utf8mb4_unicode_ci')->nullable()->after('advertiser_contact');
            $table->string('advertiser_street1', 255)->collation('utf8mb4_unicode_ci')->nullable()->after('advertiser_country');
            $table->string('advertiser_zip', 63)->collation('utf8mb4_unicode_ci')->nullable()->after('advertiser_street1');
            $table->string('advertiser_email', 255)->collation('utf8mb4_unicode_ci')->nullable()->after('advertiser_zip');
            $table->string('advertiser_phone', 255)->collation('utf8mb4_unicode_ci')->nullable()->after('advertiser_email');

            $table->integer('manager_id')->nullable()->default(0)->after('advertiser_phone');
            $table->tinyInteger('status')->nullable()->default(0)->after('request_body');
        });

        Schema::table('advertiser', function (Blueprint $table) {
            $table->string('phone', 255)->collation('utf8mb4_unicode_ci')->nullable()->change();
        });
        Schema::table('io', function (Blueprint $table) {
            $table->string('company_phone', 255)->collation('utf8mb4_unicode_ci')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pipe_drive_deal', function (Blueprint $table) {
            $table->dropColumn([
                'io_campaign_name', 'currency_id',
                'advertiser_name', 'advertiser_contact', 'advertiser_country', 'advertiser_street1', 'advertiser_zip', 'advertiser_email', 'advertiser_phone',
                'manager_id', 'status']);
        });
    }
}
