<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This recovery migration ensures the user_id column exists on all operational tables
     * even if previous migrations were interrupted or skipped.
     */
    public function up(): void
    {
        $tables = ['sales', 'clients', 'employees', 'certify_invoices', 'importation_invoices'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                if (!Schema::hasColumn($tableName, 'user_id')) {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                    });
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['sales', 'clients', 'employees', 'certify_invoices', 'importation_invoices'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'user_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                    $table->dropColumn('user_id');
                });
            }
        }
    }
};
