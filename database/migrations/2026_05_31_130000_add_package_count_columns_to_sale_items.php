<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_items', 'number_of_packages')) {
                $table->unsignedInteger('number_of_packages')->nullable()->after('package_quantity');
            }
            if (!Schema::hasColumn('sale_items', 'items_per_package')) {
                $table->unsignedInteger('items_per_package')->nullable()->after('number_of_packages');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            foreach (['items_per_package', 'number_of_packages'] as $column) {
                if (Schema::hasColumn('sale_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
