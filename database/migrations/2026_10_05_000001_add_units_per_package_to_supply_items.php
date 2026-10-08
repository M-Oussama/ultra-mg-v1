<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supply_items', function (Blueprint $table) {
            if (!Schema::hasColumn('supply_items', 'units_per_package')) {
                $table->unsignedInteger('units_per_package')->nullable()->after('quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('supply_items', function (Blueprint $table) {
            if (Schema::hasColumn('supply_items', 'units_per_package')) {
                $table->dropColumn('units_per_package');
            }
        });
    }
};
