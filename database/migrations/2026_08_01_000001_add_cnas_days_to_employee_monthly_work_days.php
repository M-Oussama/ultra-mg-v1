<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_monthly_work_days', function (Blueprint $table) {
            $table->unsignedTinyInteger('cnas_days')
                ->default(0)
                ->after('work_days');
        });
    }

    public function down(): void
    {
        Schema::table('employee_monthly_work_days', function (Blueprint $table) {
            $table->dropColumn('cnas_days');
        });
    }
};
