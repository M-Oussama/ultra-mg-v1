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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('name_ar')->nullable();
            $table->string('surname_ar')->nullable();
            $table->string('father_name_ar')->nullable();
            $table->string('mother_full_name_ar')->nullable();
            $table->unsignedBigInteger('birth_city_id')->nullable();
            $table->unsignedBigInteger('card_issued_city_id')->nullable();

            $table->foreign('birth_city_id')->references('id')->on('cities')->onDelete('restrict');
            $table->foreign('card_issued_city_id')->references('id')->on('cities')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('name_ar');
            $table->dropColumn('surname_ar');
            $table->dropColumn('father_name_ar');
            $table->dropColumn('mother_full_name_ar');
            $table->dropColumn('birth_city_id');
            $table->dropColumn('card_issued_city_id');
        });
    }
};
