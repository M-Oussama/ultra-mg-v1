<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('benefits', function (Blueprint $table) {
                $table->decimal('benefit', 20, 3)->change();
                $table->decimal('raw_material_price', 20, 3)->change();
            });

            return;
        }

        DB::statement(
            'ALTER TABLE benefits
                MODIFY benefit DECIMAL(20,3) NOT NULL DEFAULT 0,
                MODIFY raw_material_price DECIMAL(20,3) NOT NULL DEFAULT 0'
        );
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('benefits', function (Blueprint $table) {
                $table->decimal('benefit', 8, 2)->change();
                $table->decimal('raw_material_price', 8, 2)->change();
            });

            return;
        }

        DB::statement(
            'ALTER TABLE benefits
                MODIFY benefit DECIMAL(8,2) NOT NULL DEFAULT 0,
                MODIFY raw_material_price DECIMAL(8,2) NOT NULL DEFAULT 0'
        );
    }
};
