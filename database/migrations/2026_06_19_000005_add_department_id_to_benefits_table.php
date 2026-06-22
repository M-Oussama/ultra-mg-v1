<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('benefits', function (Blueprint $table) {
            if (!Schema::hasColumn('benefits', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable()->after('year');
                $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
                $table->index(['department_id', 'year', 'month'], 'benefits_department_year_month_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('benefits', function (Blueprint $table) {
            if (Schema::hasColumn('benefits', 'department_id')) {
                $table->dropIndex('benefits_department_year_month_index');
                $table->dropForeign(['department_id']);
                $table->dropColumn('department_id');
            }
        });
    }
};
