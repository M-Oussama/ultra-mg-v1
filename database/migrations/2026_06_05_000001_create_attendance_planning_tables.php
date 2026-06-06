<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_active_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['employee_id', 'month', 'year'], 'attendance_active_employees_unique');
        });

        Schema::create('employee_monthly_work_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('work_days')->default(0);
            $table->timestamps();

            $table->unique(['employee_id', 'month', 'year'], 'employee_monthly_work_days_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_monthly_work_days');
        Schema::dropIfExists('attendance_active_employees');
    }
};
