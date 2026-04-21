<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('importation_invoices', function (Blueprint $table) {
            $table->string('container_number')->nullable()->after('vessel_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('importation_invoices', function (Blueprint $table) {
            $table->dropColumn(['container_number']);
        });
    }
};
