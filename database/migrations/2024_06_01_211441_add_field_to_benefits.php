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
        Schema::table('benefits', function (Blueprint $table) {
            $table->double('electricity')->default(0);
            $table->double('employee_salary')->default(0);
            $table->double('other_charges')->default(0);
            $table->double('netBenefit')->default(0);
            $table->double('total_amount')->default(0);
            $table->double('total_articles')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('benefits', function (Blueprint $table) {
            $table->dropColumn('electricity');
            $table->dropColumn('employee_salary');
            $table->dropColumn('other_charges');
            $table->dropColumn('netBenefit');
            $table->dropColumn('total_amount');
            $table->dropColumn('total_articles');

        });
    }
};
