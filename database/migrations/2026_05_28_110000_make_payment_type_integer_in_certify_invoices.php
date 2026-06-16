<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            UPDATE certify_invoices
            SET payment_type = CASE
                WHEN payment_type IN ('1', 'Espece', 'espece', 'ESPÈCE', 'espece ') THEN '1'
                WHEN payment_type IN ('2', 'Cheque', 'cheque') THEN '2'
                WHEN payment_type IN ('3', 'Virement Bancaire', 'virement bancaire', 'virement') THEN '3'
                WHEN payment_type IN ('4', 'Versement Espece', 'versement espece') THEN '4'
                ELSE NULL
            END
        ");

        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE certify_invoices MODIFY payment_type TINYINT UNSIGNED NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certify_invoices', function (Blueprint $table) {
            $table->string('payment_type')->nullable()->change();
        });
    }
};
