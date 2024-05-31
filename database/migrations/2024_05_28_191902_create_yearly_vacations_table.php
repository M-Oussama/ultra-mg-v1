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
        Schema::create('yearly_vacations', function (Blueprint $table) {
            $table->id();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('count');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('employee_career_id');
            $table->softDeletes();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('restrict');
            $table->foreign('employee_career_id')->references('id')->on('employee_careers')->onDelete('restrict');
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
        Schema::dropIfExists('yearly_vacations');
    }
};
