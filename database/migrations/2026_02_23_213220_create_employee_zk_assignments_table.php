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
        Schema::create('employee_zk_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->unsignedBigInteger('zk_employee_id');
            $table->foreign('zk_employee_id')->references('id')->on('zk_employees')->onDelete('cascade');
            $table->dateTime('assigned_at');
            $table->dateTime('released_at')->nullable();
            $table->timestamps();

            // Indexes for faster lookups of current and historical assignments
            $table->index(['employee_id', 'released_at']);
            $table->index(['zk_employee_id', 'released_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_zk_assignments');
    }
};
