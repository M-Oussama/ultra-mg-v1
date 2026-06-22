<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_cost_components', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('name');
        });

        Schema::create('product_cost_component_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('component_id')
                ->constrained('product_cost_components')
                ->cascadeOnDelete();
            $table->decimal('amount', 18, 3)->default(0);
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['component_id', 'effective_from', 'effective_to'], 'component_prices_component_dates_index');
        });

        Schema::create('product_cost_component_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('component_id')
                ->constrained('product_cost_components')
                ->cascadeOnDelete();
            $table->decimal('quantity', 18, 3)->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'component_id'], 'component_assignments_product_component_unique');
            $table->index(['component_id', 'product_id'], 'component_assignments_component_product_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_cost_component_assignments');
        Schema::dropIfExists('product_cost_component_prices');
        Schema::dropIfExists('product_cost_components');
    }
};
