<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('import_partial_invoice_lines', function (Blueprint $table) {
            $table->decimal('real_quantity', 18, 3)->nullable();
            $table->decimal('real_unit_price', 18, 4)->nullable();
        });

        Schema::table('import_partial_invoices', function (Blueprint $table) {
            $table->decimal('real_freight_amount', 18, 4)->nullable();
            $table->string('real_invoice_number', 80)->nullable();
            $table->date('real_invoice_date')->nullable();
            $table->timestamp('loaded_at')->nullable();
        });

        Schema::table('import_parent_invoices', function (Blueprint $table) {
            $table->timestamp('domiciled_at')->nullable();
            $table->timestamp('closed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('import_partial_invoice_lines', function (Blueprint $table) {
            $table->dropColumn(['real_quantity', 'real_unit_price']);
        });

        Schema::table('import_partial_invoices', function (Blueprint $table) {
            $table->dropColumn([
                'real_freight_amount',
                'real_invoice_number',
                'real_invoice_date',
                'loaded_at',
            ]);
        });

        Schema::table('import_parent_invoices', function (Blueprint $table) {
            $table->dropColumn(['domiciled_at', 'closed_at']);
        });
    }
};
