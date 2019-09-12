<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TermTemplateGroupAddChildren extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('term_template_group', function (Blueprint $table) {
            $table->tinyInteger('show_child')->nullable()->default(1)->after('show');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('term_template_group', function (Blueprint $table) {
            $table->dropColumn(['show_child']);
        });
    }
}
