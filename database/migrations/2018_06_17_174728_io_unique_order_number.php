<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IoUniqueOrderNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('io', function (Blueprint $table) {
            $table->string('compCpc', 255)->collation('utf8mb4_unicode_ci')->nullable()->change();
            $table->string('compCpa', 255)->collation('utf8mb4_unicode_ci')->nullable()->change();
            $table->string('compCpl', 255)->collation('utf8mb4_unicode_ci')->nullable()->change();
            $table->string('compCpm', 255)->collation('utf8mb4_unicode_ci')->nullable()->change();
            $table->string('compCpd', 255)->collation('utf8mb4_unicode_ci')->nullable()->change();
            $table->string('compCpi', 255)->collation('utf8mb4_unicode_ci')->nullable()->change();
            $table->string('compCps', 255)->collation('utf8mb4_unicode_ci')->nullable()->change();
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
            $table->string('compCpc', 255)->collation('utf8mb4_unicode_ci')->change();
            $table->string('compCpa', 255)->collation('utf8mb4_unicode_ci')->change();
            $table->string('compCpl', 255)->collation('utf8mb4_unicode_ci')->change();
            $table->string('compCpm', 255)->collation('utf8mb4_unicode_ci')->change();
            $table->string('compCpd', 255)->collation('utf8mb4_unicode_ci')->change();
            $table->string('compCpi', 255)->collation('utf8mb4_unicode_ci')->change();
            $table->string('compCps', 255)->collation('utf8mb4_unicode_ci')->change();
        });
    }
}
