<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('external_id')->nullable()->after('id');
            $table->index(['external_id', 'department_id']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->string('external_id')->nullable()->after('id');
            $table->index(['external_id', 'department_id']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->string('external_id')->nullable()->after('id');
            $table->index(['external_id', 'sale_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('external_id')->nullable()->after('id');
            $table->index(['external_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex('sales_external_id_department_id_index');
            $table->dropColumn('external_id');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropIndex('sale_items_external_id_sale_id_index');
            $table->dropColumn('external_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_external_id_client_id_index');
            $table->dropColumn('external_id');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex('clients_external_id_department_id_index');
            $table->dropColumn('external_id');
        });
    }
};
