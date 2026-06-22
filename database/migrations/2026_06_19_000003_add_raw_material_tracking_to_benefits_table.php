<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('benefits', function (Blueprint $table) {
            if (!Schema::hasColumn('benefits', 'raw_material_quantity')) {
                $table->decimal('raw_material_quantity', 18, 3)->default(0)->after('raw_material_price');
            }

            if (!Schema::hasColumn('benefits', 'raw_material_cost')) {
                $table->decimal('raw_material_cost', 18, 3)->default(0)->after('raw_material_quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('benefits', function (Blueprint $table) {
            if (Schema::hasColumn('benefits', 'raw_material_cost')) {
                $table->dropColumn('raw_material_cost');
            }

            if (Schema::hasColumn('benefits', 'raw_material_quantity')) {
                $table->dropColumn('raw_material_quantity');
            }
        });
    }
};
