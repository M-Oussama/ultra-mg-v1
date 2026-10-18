<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('import_parent_invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('import_parent_invoices', 'sales_supplier_id')) {
                $table->unsignedBigInteger('sales_supplier_id')->nullable()->after('import_operation_id');
                $table->index('sales_supplier_id', 'import_parent_supplier_idx');
            }
        });
    }

    public function down(): void
    {
        Schema::table('import_parent_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('import_parent_invoices', 'sales_supplier_id')) {
                $table->dropIndex('import_parent_supplier_idx');
                $table->dropColumn('sales_supplier_id');
            }
        });
    }
};
