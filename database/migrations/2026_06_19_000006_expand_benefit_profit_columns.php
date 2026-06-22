<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('benefits', function (Blueprint $table) {
            $table->decimal('benefit', 20, 3)->change();
            $table->decimal('raw_material_price', 20, 3)->change();
        });
    }

    public function down(): void
    {
        Schema::table('benefits', function (Blueprint $table) {
            $table->decimal('benefit', 8, 2)->change();
            $table->decimal('raw_material_price', 8, 2)->change();
        });
    }
};
