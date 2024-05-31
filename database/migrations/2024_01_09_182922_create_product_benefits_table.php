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
        Schema::create('product_benefits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('benefit_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('raw_material_price');
            $table->decimal('weight');
            $table->double('benefit');
            $table->decimal('product_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_benefits');
    }
};
