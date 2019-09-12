<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IoTableAddPf extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('io', function (Blueprint $table) {
            $table->string('payment_frequency', 63)->collation('utf8mb4_unicode_ci')->nullable()->after('time');
            $table->tinyInteger('traffic_email')->nullable()->default(0)->after('traffic_social');
            $table->tinyInteger('traffic_mobile')->nullable()->default(0)->after('traffic_email');
            $table->dateTime('google_created_at')->nullable()->after('status');
            $table->text('governing_term')->collation('utf8mb4_unicode_ci')->nullable()->after('governing');
            $table->string('google_file_name', 255)->collation('utf8mb4_unicode_ci')->nullable()->after('google_file');

            $table->string('template_document', 63)->collation('utf8mb4_unicode_ci')->change();
            $table->string('company_name', 255)->collation('utf8mb4_unicode_ci')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('io', function (Blueprint $table) {
            $table->dropColumn(['payment_frequency', 'traffic_email', 'traffic_mobile', 'google_created_at', 'governing_term', 'google_file_name']);
        });
    }
}
