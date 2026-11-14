<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_has_permissions', function (Blueprint $table) {
            $table->id();
            // Keep this unconstrained for deployments whose legacy users.id
            // type or storage engine differs from Laravel's foreignId().
            $table->foreignId('user_id');
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'permission_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_has_permissions');
    }
};
