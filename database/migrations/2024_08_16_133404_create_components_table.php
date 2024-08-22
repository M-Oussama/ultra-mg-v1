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
        Schema::create('components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade'); // Foreign key to assets table
            $table->string('name'); // Name of the component
            $table->integer('maintenance_interval')->nullable(); // Interval in days for the next maintenance
            $table->integer('last_maintenance_date')->nullable(); // Interval in days for the next maintenance
            $table->integer('next_maintenance_date')->nullable(); // Interval in days for the next maintenance
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
        Schema::dropIfExists('components');
    }
};
