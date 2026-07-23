<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('money_receipt_papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('recipient_name');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('DZD');
            $table->date('payment_date');
            $table->text('reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('money_receipt_papers');
    }
};
