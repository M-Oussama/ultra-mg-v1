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
        Schema::create('importation_payments', function (Blueprint $row) {
            $row->bigIncrements('id');
            $row->unsignedBigInteger('importation_invoice_id');
            $row->decimal('amount', 15, 2);
            $row->string('type'); // invoice_settlement, customs fee, freight/transit
            $row->date('payment_date');
            $row->text('notes')->nullable();
            $row->timestamps();

            $row->foreign('importation_invoice_id')
                ->references('id')
                ->on('importation_invoices')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('importation_payments');
    }
};
