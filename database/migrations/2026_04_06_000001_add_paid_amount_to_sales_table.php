<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('sales', 'paid_amount')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->double('paid_amount')->default(0)->after('balance');
            });
        }

        // Back-fill using DB to avoid model issues during migration
        DB::table('sales')->orderBy('id')->chunk(100, function ($sales) {
            foreach ($sales as $sale) {
                $direct_payment = DB::table('payments')
                    ->where('sale_id', $sale->id)
                    ->whereNull('deleted_at')
                    ->sum('amount_paid');

                $partial_payment = DB::table('partial_payments')
                    ->where('sale_id', $sale->id)
                    ->whereNull('deleted_at')
                    ->sum('amount');

                DB::table('sales')
                    ->where('id', $sale->id)
                    ->update(['paid_amount' => (float) ($direct_payment + $partial_payment)]);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('paid_amount');
        });
    }
};
