<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_cost_components', function (Blueprint $table) {
            if (!Schema::hasColumn('product_cost_components', 'cost_type')) {
                $table->string('cost_type', 32)
                    ->default('extra')
                    ->after('name')
                    ->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_cost_components', function (Blueprint $table) {
            if (Schema::hasColumn('product_cost_components', 'cost_type')) {
                $table->dropIndex(['cost_type']);
                $table->dropColumn('cost_type');
            }
        });
    }
};
