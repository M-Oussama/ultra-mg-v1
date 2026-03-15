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
        Schema::create('sub_certify_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('certify_invoice_id');
            $table->unsignedBigInteger('fac_id')->default(0);
            $table->date('date');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->double('amount')->nullable();
            $table->string('payment_type')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('certify_invoice_id')->references('id')->on('certify_invoices')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('certify_clients')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_certify_invoices');
    }
};
