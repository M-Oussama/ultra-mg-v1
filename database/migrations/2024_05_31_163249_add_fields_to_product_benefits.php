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
        Schema::table('product_benefits', function (Blueprint $table) {
            $table->double('quantity')->default(0);
            $table->double('total_amount')->default(0);
            $table->double('total_profit')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_benefits', function (Blueprint $table) {
            $table->dropColumn('quantity');
            $table->dropColumn('total_amount');
            $table->dropColumn('total_profit');

        });
    }
};
