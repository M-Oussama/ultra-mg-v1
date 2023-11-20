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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->date('sale_date')->default(now());
            $table->unsignedBigInteger('client_id')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0.0);
            $table->unsignedBigInteger('sale_statuses_id')->nullable();
            $table->decimal('balance')->default(0);
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sale_statuses_id')->references('id')->on('sale_statuses')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
};
