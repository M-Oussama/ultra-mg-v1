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
        Schema::table('employee_careers', function (Blueprint $table) {
            $table->date('real_start_date')->nullable();
            $table->date('real_end_date')->nullable();
            $table->string('position')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_careers', function (Blueprint $table) {
            $table->dropColumn('real_start_date');
            $table->dropColumn('real_end_date');
            $table->dropColumn('position');
        });
    }
};
