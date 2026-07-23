<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('importation_invoices', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false)->after('container_status');
            $table->string('payment_status')->nullable()->after('is_paid');
            $table->decimal('freight_cost', 15, 2)->default(0)->after('amount');
            $table->decimal('customs_cost', 15, 2)->default(0)->after('freight_cost');
            $table->decimal('transport_freight_cost', 15, 2)->default(0)->after('customs_cost');
            $table->decimal('supplier_percentage_rate', 8, 2)->default(0)->after('transport_freight_cost');
        });

        Schema::table('importation_payments', function (Blueprint $table) {
            $table->decimal('percentage_rate', 8, 2)->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('importation_payments', function (Blueprint $table) {
            $table->dropColumn('percentage_rate');
        });

        Schema::table('importation_invoices', function (Blueprint $table) {
            $table->dropColumn([
                'is_paid',
                'payment_status',
                'freight_cost',
                'customs_cost',
                'transport_freight_cost',
                'supplier_percentage_rate',
            ]);
        });
    }
};
