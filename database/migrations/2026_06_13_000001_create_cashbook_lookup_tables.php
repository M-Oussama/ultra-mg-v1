<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashbook_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashbook_id')->constrained('cashbooks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cashbook_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashbook_id')->constrained('cashbooks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('name');
            $table->enum('kind', ['income', 'expense', 'both'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cashbook_payment_modes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashbook_id')->constrained('cashbooks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('name');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('contact_id')->nullable()->after('cashbook_id')->constrained('cashbook_contacts')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->after('contact_id')->constrained('cashbook_categories')->nullOnDelete();
            $table->foreignId('payment_mode_id')->nullable()->after('category_id')->constrained('cashbook_payment_modes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_mode_id');
            $table->dropConstrainedForeignId('category_id');
            $table->dropConstrainedForeignId('contact_id');
        });

        Schema::dropIfExists('cashbook_payment_modes');
        Schema::dropIfExists('cashbook_categories');
        Schema::dropIfExists('cashbook_contacts');
    }
};
