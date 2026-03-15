<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        $tables = ['certify_invoices', 'sub_certify_invoices'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->double('tva_rate')->nullable()->after('amount');
                $table->double('tva_amount')->nullable()->after('tva_rate');
                $table->double('ht_amount')->nullable()->after('tva_amount');
                $table->double('timbre_rate')->nullable()->after('ht_amount');
                $table->double('timbre_amount')->nullable()->after('timbre_rate');
                $table->string('cheque_number')->nullable()->after('timbre_amount');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = ['certify_invoices', 'sub_certify_invoices'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn([
                    'tva_rate',
                    'tva_amount',
                    'ht_amount',
                    'timbre_rate',
                    'timbre_amount',
                    'cheque_number'
                ]);
            });
        }
    }
};
