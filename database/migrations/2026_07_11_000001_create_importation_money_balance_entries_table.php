<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('importation_money_balance_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->string('group_title')->nullable();
            $table->enum('direction', ['in', 'out']);
            $table->decimal('amount', 15, 2);
            $table->text('note')->nullable();
            $table->date('entry_date')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'year', 'month']);
            $table->index(['company_id', 'year', 'month', 'group_title'], 'imb_group_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('importation_money_balance_entries');
    }
};
