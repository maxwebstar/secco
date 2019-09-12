<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MassadjustmentTableCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('massadjustment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ef_adjustment_id');
            $table->integer('affiliate_id');
            $table->integer('offer_id');
            $table->date('date');
            $table->integer('total_click');
            $table->integer('unique_click');
            $table->integer('conversion');
            $table->integer('payout');
            $table->integer('revenue');
            $table->tinyInteger('type');
            $table->text('note')->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('created_by', 63)->collation('utf8mb4_unicode_ci')->nullable();
            $table->integer('created_by_id')->nullable()->default(0);
            $table->timestamps();

            $table->unique(['affiliate_id', 'offer_id', 'date', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('massadjustment');
    }
}
