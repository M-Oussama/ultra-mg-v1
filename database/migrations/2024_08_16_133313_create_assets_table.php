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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name of the asset
            $table->string('model_number')->nullable(); // Model or serial number
            $table->string('location')->nullable(); // Location of the asset
            $table->date('last_maintenance_date')->nullable(); // Date of last maintenance
            $table->date('next_maintenance_date')->nullable(); // Date of next scheduled maintenance
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
        Schema::dropIfExists('assets');
    }
};
