<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_monthly_work_days', function (Blueprint $table) {
            $table->date('out_date')->nullable()->after('work_days');
            $table->date('in_date')->nullable()->after('out_date');
        });
    }

    public function down(): void
    {
        Schema::table('employee_monthly_work_days', function (Blueprint $table) {
            $table->dropColumn(['out_date', 'in_date']);
        });
    }
};
