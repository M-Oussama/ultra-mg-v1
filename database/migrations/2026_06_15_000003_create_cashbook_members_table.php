<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashbook_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashbook_id')->constrained('cashbooks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role')->default('viewer');
            $table->json('permissions')->nullable();
            $table->timestamps();

            $table->unique(['cashbook_id', 'user_id']);
            $table->index(['cashbook_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashbook_members');
    }
};
