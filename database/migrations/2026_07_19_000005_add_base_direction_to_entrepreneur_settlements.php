<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entrepreneur_settlements', function (Blueprint $table) {
            $table->enum('base_direction', ['in', 'out'])
                ->default('in')
                ->after('base_amount');
        });
    }

    public function down(): void
    {
        Schema::table('entrepreneur_settlements', function (Blueprint $table) {
            $table->dropColumn('base_direction');
        });
    }
};
