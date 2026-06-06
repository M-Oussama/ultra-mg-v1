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

            if ($this->indexExists('attendance_active_employees', 'attendance_active_employees_employee_id_unique')) {
                Schema::table('attendance_active_employees', function (Blueprint $table) {
                    $table->dropUnique('attendance_active_employees_employee_id_unique');
                });
            }

            if (! $this->indexExists('attendance_active_employees', 'attendance_active_employees_unique')) {
                Schema::table('attendance_active_employees', function (Blueprint $table) {
                    $table->unique(['employee_id', 'month', 'year'], 'attendance_active_employees_unique');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('attendance_active_employees')) {
            if ($this->indexExists('attendance_active_employees', 'attendance_active_employees_unique')) {
                Schema::table('attendance_active_employees', function (Blueprint $table) {
                    $table->dropUnique('attendance_active_employees_unique');
                });
            }

            if (! $this->indexExists('attendance_active_employees', 'attendance_active_employees_employee_id_unique')) {
                Schema::table('attendance_active_employees', function (Blueprint $table) {
                    $table->unique('employee_id', 'attendance_active_employees_employee_id_unique');
                });
            }
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        return DB::table('information_schema.statistics')
            ->whereRaw('table_schema = DATABASE()')
            ->where('table_name', $table)
            ->where('index_name', $index)
            ->exists();
    }
};
