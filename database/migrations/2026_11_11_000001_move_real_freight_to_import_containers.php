<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('import_partial_invoices', 'real_freight_amount')) {
            Schema::table('import_partial_invoices', function (Blueprint $table) {
                $table->dropColumn('real_freight_amount');
            });
        }

        if (! Schema::hasColumn('import_containers', 'real_freight_amount')) {
            Schema::table('import_containers', function (Blueprint $table) {
                $table->decimal('real_freight_amount', 18, 4)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('import_containers', 'real_freight_amount')) {
            Schema::table('import_containers', function (Blueprint $table) {
                $table->dropColumn('real_freight_amount');
            });
        }

        if (! Schema::hasColumn('import_partial_invoices', 'real_freight_amount')) {
            Schema::table('import_partial_invoices', function (Blueprint $table) {
                $table->decimal('real_freight_amount', 18, 4)->nullable();
            });
        }
    }
};