<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entrepreneur_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('entrepreneur_name');
            $table->string('reference')->nullable();
            $table->decimal('base_amount', 15, 2);
            $table->string('currency', 3)->default('DZD');
            $table->date('settlement_date');
            $table->enum('status', ['open', 'settled'])->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'settlement_date']);
        });

        Schema::create('entrepreneur_settlement_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('settlement_id')
                ->constrained('entrepreneur_settlements')
                ->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('direction', ['credit', 'deduction']);
            $table->decimal('amount', 15, 2);
            $table->string('description');
            $table->date('transaction_date');
            $table->timestamps();
            $table->softDeletes();

            $table->index(
                ['settlement_id', 'transaction_date'],
                'est_transactions_date_idx',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrepreneur_settlement_transactions');
        Schema::dropIfExists('entrepreneur_settlements');
    }
};
