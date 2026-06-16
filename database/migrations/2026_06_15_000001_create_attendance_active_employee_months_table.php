<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_active_employee_months', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('copied_from_month')->nullable();
            $table->unsignedSmallInteger('copied_from_year')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->unique(['month', 'year'], 'attendance_active_employee_months_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_active_employee_months');
    }
};
