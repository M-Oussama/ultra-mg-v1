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
        Schema::table('certify_clients', function (Blueprint $table) {
            $table->boolean('is_sub_certify')
                ->default(false)
                ->after('is_nif_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('certify_clients', function (Blueprint $table) {
            $table->dropColumn('is_sub_certify');
        });
    }
};
