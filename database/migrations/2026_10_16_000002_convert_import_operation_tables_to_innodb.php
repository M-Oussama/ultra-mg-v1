<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        foreach ([
            'import_operations',
            'import_parent_invoices',
            'import_parent_invoice_lines',
            'import_partial_invoices',
            'import_partial_invoice_lines',
            'import_audit_events',
        ] as $table) {
            if (Schema::hasTable($table)) {
                DB::statement("ALTER TABLE `{$table}` ENGINE=InnoDB");
            }
        }

        Schema::table('import_parent_invoices', function (Blueprint $table) {
            $table->foreign('import_operation_id', 'import_parent_operation_fk')
                ->references('id')->on('import_operations')->cascadeOnDelete();
        });
        Schema::table('import_parent_invoice_lines', function (Blueprint $table) {
            $table->foreign('import_parent_invoice_id', 'import_parent_lines_parent_fk')
                ->references('id')->on('import_parent_invoices')->cascadeOnDelete();
        });
        Schema::table('import_partial_invoices', function (Blueprint $table) {
            $table->foreign('import_parent_invoice_id', 'import_partial_parent_fk')
                ->references('id')->on('import_parent_invoices')->cascadeOnDelete();
        });
        Schema::table('import_partial_invoice_lines', function (Blueprint $table) {
            $table->foreign('import_partial_invoice_id', 'import_partial_lines_partial_fk')
                ->references('id')->on('import_partial_invoices')->cascadeOnDelete();
            $table->foreign('import_parent_invoice_line_id', 'import_partial_lines_parent_line_fk')
                ->references('id')->on('import_parent_invoice_lines')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('import_partial_invoice_lines', function (Blueprint $table) {
            $table->dropForeign('import_partial_lines_partial_fk');
            $table->dropForeign('import_partial_lines_parent_line_fk');
        });
        Schema::table('import_partial_invoices', function (Blueprint $table) {
            $table->dropForeign('import_partial_parent_fk');
        });
        Schema::table('import_parent_invoice_lines', function (Blueprint $table) {
            $table->dropForeign('import_parent_lines_parent_fk');
        });
        Schema::table('import_parent_invoices', function (Blueprint $table) {
            $table->dropForeign('import_parent_operation_fk');
        });
    }
};
