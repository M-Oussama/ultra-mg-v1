<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('certify_clients', function (Blueprint $table) {
            $table->boolean('is_cnrc_active')->default(false)->after('NRC');
            $table->boolean('is_nif_active')->default(false)->after('NIF');
            $table->string('profession')->nullable()->after('surname');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certify_clients', function (Blueprint $table) {
            $table->dropColumn(['is_cnrc_active', 'is_nif_active', 'profession']);
        });
    }
};

