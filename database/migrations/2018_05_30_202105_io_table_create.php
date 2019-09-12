<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IoTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('io', function (Blueprint $table) {

            $table->increments('id');
            $table->string('campaign_name', 255)->collation('utf8mb4_unicode_ci');
            $table->integer('advertiser_id');
            $table->string('currency', 7)->collation('utf8mb4_unicode_ci')->nullable();

            $table->string('compCpc', 255)->collation('utf8mb4_unicode_ci');
            $table->string('compCpa', 255)->collation('utf8mb4_unicode_ci');
            $table->string('compCpl', 255)->collation('utf8mb4_unicode_ci');
            $table->string('compCpm', 255)->collation('utf8mb4_unicode_ci');
            $table->string('compCpd', 255)->collation('utf8mb4_unicode_ci');
            $table->string('compCpi', 255)->collation('utf8mb4_unicode_ci');
            $table->string('compCps', 255)->collation('utf8mb4_unicode_ci');

            $table->tinyInteger('traffic_search')->nullable()->default(0);
            $table->tinyInteger('traffic_banner')->nullable()->default(0);
            $table->tinyInteger('traffic_popup')->nullable()->default(0);
            $table->tinyInteger('traffic_context')->nullable()->default(0);
            $table->tinyInteger('traffic_exit')->nullable()->default(0);
            $table->tinyInteger('traffic_incent')->nullable()->default(0);
            $table->tinyInteger('traffic_path')->nullable()->default(0);
            $table->tinyInteger('traffic_social')->nullable()->default(0);
            $table->string('traffic_incent_name', 63)->collation('utf8mb4_unicode_ci')->nullable();

            $table->string('secco_contact', 255)->collation('utf8mb4_unicode_ci');
            $table->string('secco_email', 255)->collation('utf8mb4_unicode_ci');
            $table->string('secco_phone', 63)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('secco_fax', 63)->collation('utf8mb4_unicode_ci')->nullable();

            $table->string('company_name', 255)->collation('utf8mb4_unicode_ci');
            $table->string('company_contact', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('company_email', 255)->collation('utf8mb4_unicode_ci');

            $table->string('company_country', 7)->collation('utf8mb4_unicode_ci');
            $table->string('company_state', 7)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('company_city', 255)->collation('utf8mb4_unicode_ci');
            $table->string('company_street1', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('company_street2', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('company_zip', 63)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('company_phone', 63)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('company_fax', 63)->collation('utf8mb4_unicode_ci')->nullable();

            $table->string('billing_contact', 255)->collation('utf8mb4_unicode_ci');
            $table->string('billing_street1', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('billing_street2', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('billing_country', 7)->collation('utf8mb4_unicode_ci');
            $table->string('billing_state', 7)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('billing_city', 255)->collation('utf8mb4_unicode_ci');
            $table->string('billing_zip', 63)->collation('utf8mb4_unicode_ci')->nullable();

            $table->tinyInteger('prepay')->nullable()->default(0);
            $table->integer('prepay_amount')->nullable()->default(0);

            $table->string('gov_type', 15)->collation('utf8mb4_unicode_ci')->nullable(); /* now, date */
            $table->date('gov_date')->nullable();
            $table->tinyInteger('governing')->nullable()->default(0);

            $table->tinyInteger('status')->nullable()->default(0);
            $table->string('google_url', 255)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('google_folder', 63)->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('google_file', 63)->collation('utf8mb4_unicode_ci')->nullable();

            $table->integer('term_id')->nullable()->default(0);
            $table->string('template_document', 7)->collation('utf8mb4_unicode_ci');

            $table->text('note')->collation('utf8mb4_unicode_ci')->nullable();
            $table->dateTime('time')->nullable();

            $table->string('created_by', 63)->collation('utf8mb4_unicode_ci');
            $table->integer('created_by_id');

            $table->timestamps();

            $table->integer('order_number')->nullable();
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
        Schema::dropIfExists('io');
    }
}
