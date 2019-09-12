<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TermTemplateRemoveGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('term_template', function (Blueprint $table) {
            $table->dropColumn(['group_id']);
        });

        Schema::dropIfExists('term_template_group');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('term_template', function (Blueprint $table) {
            //
        });
    }
}
