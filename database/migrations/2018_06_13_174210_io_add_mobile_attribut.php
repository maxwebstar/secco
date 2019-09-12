<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IoAddMobileAttribut extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('io', function (Blueprint $table) {
            $table->integer('advertiser_id')->nullable()->default(0)->change();

            $table->string('mobile_attribut_platform', 255)->collation('utf8mb4_unicode_ci')->nullable()->after('term_id');
            $table->string('template_document_custom', 255)->collation('utf8mb4_unicode_ci')->nullable()->after('template_document');
            $table->string('frequency_custom', 255)->collation('utf8mb4_unicode_ci')->nullable()->after('frequency_id');
            $table->integer('template_document_id')->nullable()->default(0)->after('template_document');

            $table->dropColumn(['template_document']);
        });

        Schema::table('advertiser', function (Blueprint $table) {
            $table->string('frequency_custom', 255)->collation('utf8mb4_unicode_ci')->nullable()->after('frequency_id');
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
            $table->dropColumn(['mobile_attribut_platform', 'template_document_custom', 'frequency_custom', 'template_document_id']);
        });

        Schema::table('advertiser', function (Blueprint $table) {
            $table->dropColumn(['frequency_custom']);
        });
    }
}
