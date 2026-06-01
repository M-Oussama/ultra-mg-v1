<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'package_type')) {
                $table->string('package_type')->nullable()->after('stockable');
            }
            if (!Schema::hasColumn('products', 'units_per_package')) {
                $table->unsignedInteger('units_per_package')->nullable()->after('package_type');
            }
        });

        Schema::table('sale_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_items', 'package_type')) {
                $table->string('package_type')->nullable()->after('price');
            }
            if (!Schema::hasColumn('sale_items', 'units_per_package')) {
                $table->unsignedInteger('units_per_package')->nullable()->after('package_type');
            }
            if (!Schema::hasColumn('sale_items', 'package_quantity')) {
                $table->unsignedInteger('package_quantity')->nullable()->after('units_per_package');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            foreach (['package_quantity', 'units_per_package', 'package_type'] as $column) {
                if (Schema::hasColumn('sale_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('products', function (Blueprint $table) {
            foreach (['units_per_package', 'package_type'] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
