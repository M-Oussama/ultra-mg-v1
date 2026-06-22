<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_monthly_payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->decimal('monthly_salary', 15, 2)->default(0);
            $table->decimal('objectives_amount', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['employee_id', 'month', 'year'], 'employee_monthly_payrolls_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_monthly_payrolls');
    }
};
