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
            $table->string('vessel_name')->nullable()->after('container_status');
            $table->string('vessel_number')->nullable()->after('vessel_name');
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
            $table->dropColumn(['vessel_name', 'vessel_number']);
        });
    }
};
