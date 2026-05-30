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
        Schema::table('certify_invoice_products', function (Blueprint $table) {
            $table->decimal('total', 20, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certify_invoice_products', function (Blueprint $table) {
            $table->unsignedBigInteger('total')->change();
        });
    }
};

