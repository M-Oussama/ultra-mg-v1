<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('before_quantity', 18, 3)->default(0);
            $table->decimal('delta_quantity', 18, 3);
            $table->decimal('after_quantity', 18, 3)->default(0);
            $table->string('reason', 100);
            $table->string('source_type', 100)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->text('note')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'created_at']);
            $table->index(['source_type', 'source_id']);
            $table->index(['department_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stock_adjustments');
    }
};
