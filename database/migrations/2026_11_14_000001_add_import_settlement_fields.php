<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('customs_percentage', 8, 4)->default(0)->after('tax_rate');
        });
        Schema::table('import_parent_invoice_lines', function (Blueprint $table) {
            $table->decimal('customs_percentage_snapshot', 8, 4)->default(0)->after('commercial_unit_price');
        });
        Schema::table('import_partial_invoices', function (Blueprint $table) {
            $table->decimal('agent_percentage', 8, 4)->default(0)->after('freight_amount');
            $table->decimal('bank_exchange_rate_dzd', 18, 4)->nullable()->after('agent_percentage');
        });
        Schema::table('import_containers', function (Blueprint $table) {
            $table->decimal('transitaire_fee_dzd', 18, 4)->default(0)->after('real_freight_amount');
        });
    }

    public function down(): void
    {
        Schema::table('import_containers', fn (Blueprint $table) => $table->dropColumn('transitaire_fee_dzd'));
        Schema::table('import_partial_invoices', fn (Blueprint $table) => $table->dropColumn(['agent_percentage', 'bank_exchange_rate_dzd']));
        Schema::table('import_parent_invoice_lines', fn (Blueprint $table) => $table->dropColumn('customs_percentage_snapshot'));
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn('customs_percentage'));
    }
};
