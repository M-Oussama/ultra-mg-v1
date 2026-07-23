<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('importation_money_balance_entries', function (Blueprint $table) {
            $table->decimal('in_usd', 15, 2)->nullable()->after('note');
            $table->decimal('out_usd', 15, 2)->nullable()->after('in_usd');
            $table->decimal('rest_usd', 15, 2)->nullable()->after('out_usd');
            $table->decimal('total_5_percent', 15, 2)->nullable()->after('rest_usd');
            $table->decimal('half_out_usd', 15, 2)->nullable()->after('total_5_percent');
            $table->decimal('rest_percent', 15, 2)->nullable()->after('half_out_usd');
            $table->decimal('total_in', 15, 2)->nullable()->after('rest_percent');
            $table->decimal('containers_fees', 15, 2)->nullable()->after('total_in');
            $table->decimal('total_out', 15, 2)->nullable()->after('containers_fees');
        });
    }

    public function down(): void
    {
        Schema::table('importation_money_balance_entries', function (Blueprint $table) {
            $table->dropColumn([
                'in_usd',
                'out_usd',
                'rest_usd',
                'total_5_percent',
                'half_out_usd',
                'rest_percent',
                'total_in',
                'containers_fees',
                'total_out',
            ]);
        });
    }
};
