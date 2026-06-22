<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('benefit_extra_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('benefit_id')->constrained('benefits')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('amount', 18, 3)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['benefit_id', 'name'], 'benefit_extra_fees_benefit_name_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('benefit_extra_fees');
    }
};
