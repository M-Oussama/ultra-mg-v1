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
        Schema::create('importation_invoices', function (Blueprint $row) {
            $row->bigIncrements('id');
            $row->unsignedBigInteger('supplier_id');
            $row->string('invoice_number');
            $row->string('BL_number');
            $row->string('company_name');
            $row->date('start_date');
            $row->date('arrive_date');
            $row->decimal('amount', 15, 2);
            $row->string('container_status');
            $row->text('notes')->nullable();
            $row->string('invoice_pdf')->nullable();
            $row->timestamps();

            $row->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('importation_invoices');
    }
};
