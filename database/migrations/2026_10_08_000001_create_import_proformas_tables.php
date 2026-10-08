<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_proformas', function (Blueprint $table) {
            $table->id();
            $table->string('number', 80)->unique();
            $table->date('proforma_date');
            $table->foreignId('sales_supplier_id')
                ->constrained('sales_suppliers')
                ->cascadeOnDelete();
            $table->unsignedBigInteger('departement_id');
            $table->foreign('departement_id')
                ->references('id')
                ->on('departments')
                ->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('currency', 3)->default('USD');
            $table->unsignedBigInteger('total_quantity')->default(0);
            $table->decimal('total_amount', 18, 4)->default(0);
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['departement_id', 'proforma_date']);
            $table->index(['sales_supplier_id', 'proforma_date']);
        });

        Schema::create('import_proforma_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_proforma_id')
                ->constrained('import_proformas')
                ->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()
                ->constrained('products')
                ->nullOnDelete();
            // This supplier-facing name is deliberately independent from
            // products.name so a proforma edit never renames the catalog.
            $table->string('product_name');
            $table->string('reference')->nullable();
            $table->unsignedBigInteger('quantity');
            $table->decimal('unit_price', 18, 4);
            $table->decimal('total_price', 18, 4);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_proforma_items');
        Schema::dropIfExists('import_proformas');
    }
};
