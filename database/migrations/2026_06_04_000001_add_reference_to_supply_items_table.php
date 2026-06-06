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
        if (!Schema::hasColumn('supply_items', 'reference')) {
            Schema::table('supply_items', function (Blueprint $table) {
                $table->string('reference', 20)->nullable()->after('sales_supplier_id');
            });
        }

        $rows = DB::table('supply_items')
            ->join('supplies', 'supplies.id', '=', 'supply_items.supply_id')
            ->select(
                'supply_items.id',
                'supply_items.product_id',
                'supplies.departement_id',
                'supplies.supply_date'
            )
            ->orderBy('supplies.departement_id')
            ->orderBy('supply_items.product_id')
            ->orderBy('supplies.supply_date')
            ->orderBy('supplies.id')
            ->orderBy('supply_items.id')
            ->get();

        $sequenceByKey = [];

        foreach ($rows as $row) {
            $key = $row->departement_id . ':' . $row->product_id;
            $sequenceByKey[$key] = ($sequenceByKey[$key] ?? 0) + 1;

            DB::table('supply_items')
                ->where('id', $row->id)
                ->update([
                    'reference' => str_pad((string) $sequenceByKey[$key], 4, '0', STR_PAD_LEFT),
                ]);
        }

        DB::statement('ALTER TABLE supply_items MODIFY reference VARCHAR(20) NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('supply_items', 'reference')) {
            Schema::table('supply_items', function (Blueprint $table) {
                $table->dropColumn('reference');
            });
        }
    }
};
