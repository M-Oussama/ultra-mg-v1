<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('import_containers', function (Blueprint $table) {
            if (! Schema::hasColumn('import_containers', 'import_parent_invoice_id')) {
                $table->unsignedBigInteger('import_parent_invoice_id')->nullable()->after('import_operation_id');
                $table->foreign('import_parent_invoice_id', 'import_container_parent_fk')
                    ->references('id')->on('import_parent_invoices')->nullOnDelete();
                $table->index('import_parent_invoice_id', 'import_container_parent_idx');
            }
            if (! Schema::hasColumn('import_containers', 'freight_amount')) {
                $table->decimal('freight_amount', 18, 4)->default(0)->after('booking_reference');
            }
            if (! Schema::hasColumn('import_containers', 'container_size')) {
                $table->string('container_size', 30)->nullable()->after('freight_amount');
            }
        });

        Schema::table('import_costs', function (Blueprint $table) {
            if (! Schema::hasColumn('import_costs', 'import_parent_invoice_id')) {
                $table->unsignedBigInteger('import_parent_invoice_id')->nullable()->after('import_operation_id');
                $table->foreign('import_parent_invoice_id', 'import_cost_parent_fk')
                    ->references('id')->on('import_parent_invoices')->nullOnDelete();
                $table->index('import_parent_invoice_id', 'import_cost_parent_idx');
            }
        });
    }

    public function down(): void
    {
        Schema::table('import_costs', function (Blueprint $table) {
            if (Schema::hasColumn('import_costs', 'import_parent_invoice_id')) {
                $table->dropForeign('import_cost_parent_fk');
                $table->dropIndex('import_cost_parent_idx');
                $table->dropColumn('import_parent_invoice_id');
            }
        });

        Schema::table('import_containers', function (Blueprint $table) {
            if (Schema::hasColumn('import_containers', 'import_parent_invoice_id')) {
                $table->dropForeign('import_container_parent_fk');
                $table->dropIndex('import_container_parent_idx');
                $table->dropColumn('import_parent_invoice_id');
            }
            if (Schema::hasColumn('import_containers', 'freight_amount')) {
                $table->dropColumn('freight_amount');
            }
            if (Schema::hasColumn('import_containers', 'container_size')) {
                $table->dropColumn('container_size');
            }
        });
    }
};
