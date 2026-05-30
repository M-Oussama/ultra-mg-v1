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
        Schema::table('cheques', function (Blueprint $table) {
            $table->string('pdf_name')->nullable()->after('file_path');
            $table->unsignedBigInteger('company_id')->nullable()->after('client_id');
            $table->text('notes')->nullable()->after('banque');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cheques', function (Blueprint $table) {
            $table->dropColumn(['pdf_name', 'company_id', 'notes']);
        });
    }
};

