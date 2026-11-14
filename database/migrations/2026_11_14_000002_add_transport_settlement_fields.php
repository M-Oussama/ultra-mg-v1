<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('import_partial_invoices', function (Blueprint $table) {
            $table->decimal('transport_amount', 18, 4)->default(0)->after('bank_exchange_rate_dzd');
            $table->decimal('transport_customs_percentage', 8, 4)->default(0)->after('transport_amount');
            $table->decimal('transport_exchange_rate_dzd', 18, 4)->nullable()->after('transport_customs_percentage');
        });
    }

    public function down(): void
    {
        Schema::table('import_partial_invoices', function (Blueprint $table) {
            $table->dropColumn([
                'transport_amount',
                'transport_customs_percentage',
                'transport_exchange_rate_dzd',
            ]);
        });
    }
};
