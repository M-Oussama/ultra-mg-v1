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
        Schema::create('sub_certify_invoice_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_certify_invoice_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('quantity');
            $table->unsignedBigInteger('total');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sub_certify_invoice_id', 'sub_inv_prod_sub_inv_id_foreign')->references('id')->on('sub_certify_invoices')->onDelete('cascade');
            $table->foreign('product_id', 'sub_inv_prod_prod_id_foreign')->references('id')->on('certify_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_certify_invoice_products');
    }
};
