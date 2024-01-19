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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('description')->nullable();
            $table->string('product_code')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('SKU')->nullable();
            $table->integer('min_stock_level')->default(0);
            $table->decimal('price', 10, 2);
            $table->decimal('weight')->default(0);
            $table->boolean('stockable')->default(0);
            $table->decimal('tax_rate', 5, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
