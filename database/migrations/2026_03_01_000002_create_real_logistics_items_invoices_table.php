<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('real_logistics_items_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('real_logistics_invoice_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('product_name')->nullable();
            $table->double('quantity');
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('real_logistics_invoice_id', 'rl_invoice_id_foreign')
                ->references('id')->on('real_logistics_invoices')
                ->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_logistics_items_invoices');
    }
};
