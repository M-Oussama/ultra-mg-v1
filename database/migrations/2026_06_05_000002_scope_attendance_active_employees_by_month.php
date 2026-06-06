<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $defaultMonth = (int) date('n');
        $defaultYear = (int) date('Y');

        if (Schema::hasTable('attendance_active_employees')) {
            Schema::table('attendance_active_employees', function (Blueprint $table) use ($defaultMonth, $defaultYear) {
                if (!Schema::hasColumn('attendance_active_employees', 'month')) {
                    $table->unsignedTinyInteger('month')->default($defaultMonth)->after('employee_id');
                }

                if (!Schema::hasColumn('attendance_active_employees', 'year')) {
                    $table->unsignedSmallInteger('year')->default($defaultYear)->after('month');
                }
            });

            DB::table('attendance_active_employees')
                ->whereNull('month')
                ->update(['month' => $defaultMonth]);

            DB::table('attendance_active_employees')
                ->whereNull('year')
                ->update(['year' => $defaultYear]);

            Schema::table('attendance_active_employees', function (Blueprint $table) {
                try {
                    $table->dropUnique('attendance_active_employees_employee_id_unique');
                } catch (\Throwable $e) {
                    // Ignore if the legacy unique index does not exist.
                }

                try {
                    $table->unique(['employee_id', 'month', 'year'], 'attendance_active_employees_unique');
                } catch (\Throwable $e) {
                    // Ignore if the composite index already exists.
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('attendance_active_employees')) {
            Schema::table('attendance_active_employees', function (Blueprint $table) {
                try {
                    $table->dropUnique('attendance_active_employees_unique');
                } catch (\Throwable $e) {
                    // Ignore if it does not exist.
                }

                try {
                    $table->unique('employee_id', 'attendance_active_employees_employee_id_unique');
                } catch (\Throwable $e) {
                    // Ignore if it already exists.
                }
            });
        }
    }
};
