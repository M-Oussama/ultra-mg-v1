<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('yearly_vacations', function (Blueprint $table) {
            if (!Schema::hasColumn('yearly_vacations', 'vacation_year')) {
                $table->string('vacation_year')->nullable()->after('count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('yearly_vacations', function (Blueprint $table) {
            if (Schema::hasColumn('yearly_vacations', 'vacation_year')) {
                $table->dropColumn('vacation_year');
            }
        });
    }
};
