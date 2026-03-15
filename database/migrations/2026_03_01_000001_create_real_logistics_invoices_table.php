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
        Schema::create('real_logistics_invoices', function (Blueprint $table) {
            $table->id();
            $table->date('invoice_date')->useCurrent();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->string('status')->default('pending');
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_logistics_invoices');
    }
};
