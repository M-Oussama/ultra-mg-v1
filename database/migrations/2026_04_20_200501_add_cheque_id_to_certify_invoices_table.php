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
        Schema::table('certify_invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('cheque_id')->nullable();
            $table->foreign('cheque_id')->references('id')->on('cheques')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('certify_invoices', function (Blueprint $table) {
            $table->dropForeign(['cheque_id']);
            $table->dropColumn('cheque_id');
        });
    }
};
