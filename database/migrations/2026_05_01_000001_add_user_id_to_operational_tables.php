<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        if (Schema::hasTable('certify_invoices')) {
            Schema::table('certify_invoices', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        if (Schema::hasTable('importation_invoices')) {
            Schema::table('importation_invoices', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
};
