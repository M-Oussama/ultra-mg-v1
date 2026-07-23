<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settlement_entrepreneurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'name'], 'settlement_entrepreneurs_company_name_unique');
        });

        Schema::table('entrepreneur_settlements', function (Blueprint $table) {
            $table->foreignId('entrepreneur_id')
                ->nullable()
                ->after('company_id')
                ->constrained('settlement_entrepreneurs')
                ->nullOnDelete();
            $table->index('entrepreneur_id', 'entrepreneur_settlements_entrepreneur_idx');
        });
    }

    public function down(): void
    {
        Schema::table('entrepreneur_settlements', function (Blueprint $table) {
            $table->dropForeign(['entrepreneur_id']);
            $table->dropIndex('entrepreneur_settlements_entrepreneur_idx');
            $table->dropColumn('entrepreneur_id');
        });

        Schema::dropIfExists('settlement_entrepreneurs');
    }
};
