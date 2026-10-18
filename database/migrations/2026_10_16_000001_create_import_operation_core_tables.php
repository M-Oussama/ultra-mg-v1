<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('import_operations')) {
            Schema::create('import_operations', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->id();
                // Legacy tables use MyISAM in existing installations, so these
                // references are validated by the application and indexed here.
                $table->unsignedBigInteger('legacy_importation_invoice_id')->nullable();
                $table->unsignedBigInteger('exporter_supplier_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->string('reference', 80)->unique();
                $table->string('currency', 3);
                $table->string('origin', 120)->nullable();
                $table->string('destination', 120)->nullable();
                $table->string('status', 30)->default('draft');
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['status', 'created_at'], 'import_operations_status_date_idx');
                $table->index('legacy_importation_invoice_id', 'import_operations_legacy_idx');
                $table->index('exporter_supplier_id', 'import_operations_exporter_idx');
                $table->index('created_by', 'import_operations_creator_idx');
            });
        }

        if (! Schema::hasTable('import_parent_invoices')) {
            Schema::create('import_parent_invoices', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->id();
                $table->unsignedBigInteger('import_operation_id');
                $table->string('number', 80);
                $table->string('currency', 3);
                $table->unsignedInteger('planned_container_count')->default(0);
                $table->decimal('freight_per_container', 18, 4)->default(0);
                $table->string('status', 30)->default('draft');
                $table->unsignedInteger('revision')->default(1);
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['import_operation_id', 'number'], 'import_parent_operation_number_uq');
                $table->index(['import_operation_id', 'status'], 'import_parent_operation_status_idx');
            });
        }

        if (! Schema::hasTable('import_parent_invoice_lines')) {
            Schema::create('import_parent_invoice_lines', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->id();
                $table->unsignedBigInteger('import_parent_invoice_id');
                $table->unsignedBigInteger('product_id')->nullable();
                $table->string('description_snapshot');
                $table->string('unit', 30)->default('pcs');
                $table->decimal('quantity', 18, 3);
                $table->decimal('commercial_unit_price', 18, 4);
                $table->timestamps();

                $table->index(
                    ['import_parent_invoice_id', 'product_id'],
                    'import_parent_lines_parent_product_idx'
                );
                $table->index('product_id', 'import_parent_lines_product_idx');
            });
        }

        if (! Schema::hasTable('import_partial_invoices')) {
            Schema::create('import_partial_invoices', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->id();
                $table->unsignedBigInteger('import_parent_invoice_id');
                $table->string('number', 80);
                $table->unsignedInteger('container_allocation_count')->default(1);
                $table->decimal('freight_amount', 18, 4)->default(0);
                $table->string('status', 30)->default('draft');
                $table->unsignedInteger('revision')->default(1);
                $table->string('idempotency_key', 100)->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['import_parent_invoice_id', 'number'], 'import_partial_parent_number_uq');
                $table->unique(
                    ['import_parent_invoice_id', 'idempotency_key'],
                    'import_partial_parent_idempotency_uq'
                );
                $table->index(['import_parent_invoice_id', 'status'], 'import_partial_parent_status_idx');
            });
        }

        if (! Schema::hasTable('import_partial_invoice_lines')) {
            Schema::create('import_partial_invoice_lines', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->id();
                $table->unsignedBigInteger('import_partial_invoice_id');
                $table->unsignedBigInteger('import_parent_invoice_line_id');
                $table->decimal('quantity', 18, 3);
                $table->decimal('commercial_unit_price_snapshot', 18, 4);
                $table->timestamps();

                $table->unique(
                    ['import_partial_invoice_id', 'import_parent_invoice_line_id'],
                    'import_partial_line_parent_unique'
                );
                $table->index('import_parent_invoice_line_id', 'import_partial_lines_parent_line_idx');
            });
        }

        if (! Schema::hasTable('import_audit_events')) {
            Schema::create('import_audit_events', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->id();
                $table->string('entity_type', 100);
                $table->unsignedBigInteger('entity_id');
                $table->string('action', 50);
                $table->unsignedBigInteger('actor_id')->nullable();
                $table->json('before_json')->nullable();
                $table->json('after_json')->nullable();
                $table->text('reason')->nullable();
                $table->timestamp('occurred_at');

                $table->index(['entity_type', 'entity_id', 'occurred_at'], 'import_audit_entity_index');
                $table->index('actor_id', 'import_audit_actor_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('import_audit_events');
        Schema::dropIfExists('import_partial_invoice_lines');
        Schema::dropIfExists('import_partial_invoices');
        Schema::dropIfExists('import_parent_invoice_lines');
        Schema::dropIfExists('import_parent_invoices');
        Schema::dropIfExists('import_operations');
    }
};
