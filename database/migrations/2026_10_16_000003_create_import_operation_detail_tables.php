<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_factory_orders', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('import_operation_id');
            $table->unsignedBigInteger('factory_supplier_id')->nullable();
            $table->string('number', 80)->nullable();
            $table->string('currency', 3);
            $table->string('status', 30)->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('import_operation_id', 'import_factory_operation_fk')->references('id')->on('import_operations')->cascadeOnDelete();
            $table->index(['import_operation_id', 'status'], 'import_factory_operation_status_idx');
            $table->index('factory_supplier_id', 'import_factory_supplier_idx');
        });
        Schema::create('import_factory_order_lines', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('import_factory_order_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('description_snapshot');
            $table->string('unit', 30)->default('pcs');
            $table->decimal('quantity', 18, 3);
            $table->decimal('unit_price', 18, 4);
            $table->timestamps();
            $table->foreign('import_factory_order_id', 'import_factory_lines_order_fk')->references('id')->on('import_factory_orders')->cascadeOnDelete();
            $table->index('product_id', 'import_factory_lines_product_idx');
        });
        Schema::create('import_containers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('import_operation_id');
            $table->unsignedBigInteger('import_partial_invoice_id')->nullable();
            $table->string('container_number', 80)->nullable();
            $table->string('seal_number', 80)->nullable();
            $table->string('booking_reference', 100)->nullable();
            $table->string('status', 30)->default('planned');
            $table->timestamps();
            $table->foreign('import_operation_id', 'import_container_operation_fk')->references('id')->on('import_operations')->cascadeOnDelete();
            $table->foreign('import_partial_invoice_id', 'import_container_partial_fk')->references('id')->on('import_partial_invoices')->nullOnDelete();
            $table->index(['import_operation_id', 'status'], 'import_container_operation_status_idx');
        });
        Schema::create('import_costs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('import_operation_id');
            $table->unsignedBigInteger('import_partial_invoice_id')->nullable();
            $table->string('category', 50);
            $table->string('value_type', 10);
            $table->decimal('amount', 18, 4);
            $table->string('currency', 3);
            $table->decimal('exchange_rate', 18, 8)->nullable();
            $table->date('exchange_rate_date')->nullable();
            $table->string('exchange_rate_source', 120)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('import_operation_id', 'import_cost_operation_fk')->references('id')->on('import_operations')->cascadeOnDelete();
            $table->foreign('import_partial_invoice_id', 'import_cost_partial_fk')->references('id')->on('import_partial_invoices')->nullOnDelete();
            $table->index(['import_operation_id', 'value_type'], 'import_cost_operation_type_idx');
        });
        Schema::create('import_commissions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('import_operation_id');
            $table->unsignedBigInteger('import_partial_invoice_id')->nullable();
            $table->decimal('percentage', 8, 4);
            $table->string('base_type', 50);
            $table->decimal('base_amount', 18, 4);
            $table->decimal('calculated_amount', 18, 4);
            $table->string('currency', 3);
            $table->timestamps();
            $table->foreign('import_operation_id', 'import_commission_operation_fk')->references('id')->on('import_operations')->cascadeOnDelete();
            $table->foreign('import_partial_invoice_id', 'import_commission_partial_fk')->references('id')->on('import_partial_invoices')->nullOnDelete();
        });
        Schema::create('import_documents', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('import_operation_id');
            $table->unsignedBigInteger('import_parent_invoice_id')->nullable();
            $table->unsignedBigInteger('import_partial_invoice_id')->nullable();
            $table->string('type', 50);
            $table->unsignedInteger('version');
            $table->string('storage_path');
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->string('sha256', 64);
            $table->string('issuer', 120)->nullable();
            $table->string('status', 30)->default('exporter_issued');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();
            $table->foreign('import_operation_id', 'import_document_operation_fk')->references('id')->on('import_operations')->cascadeOnDelete();
            $table->foreign('import_parent_invoice_id', 'import_document_parent_fk')->references('id')->on('import_parent_invoices')->nullOnDelete();
            $table->foreign('import_partial_invoice_id', 'import_document_partial_fk')->references('id')->on('import_partial_invoices')->nullOnDelete();
            $table->unique(['import_operation_id', 'type', 'version'], 'import_document_type_version_uq');
        });
        Schema::create('import_operation_payments', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('import_operation_id');
            $table->unsignedBigInteger('import_partial_invoice_id')->nullable();
            $table->decimal('amount', 18, 4);
            $table->string('currency', 3);
            $table->date('paid_at');
            $table->string('reference', 120)->nullable();
            $table->string('status', 30)->default('recorded');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('import_operation_id', 'import_payment_operation_fk')->references('id')->on('import_operations')->cascadeOnDelete();
            $table->foreign('import_partial_invoice_id', 'import_payment_partial_fk')->references('id')->on('import_partial_invoices')->nullOnDelete();
            $table->index(['import_operation_id', 'paid_at'], 'import_payment_operation_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_operation_payments');
        Schema::dropIfExists('import_documents');
        Schema::dropIfExists('import_commissions');
        Schema::dropIfExists('import_costs');
        Schema::dropIfExists('import_containers');
        Schema::dropIfExists('import_factory_order_lines');
        Schema::dropIfExists('import_factory_orders');
    }
};
