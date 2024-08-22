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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('component_id')->constrained()->onDelete('cascade'); // Foreign key to components table
            $table->foreignId('asset_id')->constrained()->onDelete('cascade'); // Foreign key to components table
            $table->date('maintenance_date'); // Date when maintenance was performed
            $table->unsignedBigInteger('technician_id')->nullable(); // Name or ID of the technician
            $table->unsignedBigInteger('technician_assigned_id')->nullable(); // Name or ID of the technician
            $table->date('last_maintenance_date')->nullable(); // Date of last maintenance
            $table->integer('maintenance_interval')->nullable(); // Interval in days for the next maintenance
            $table->date('next_maintenance_date')->nullable(); // Interval in days for the next maintenance
            $table->text('notes')->nullable(); // Additional notes or observations
            $table->string('status')->default('active'); // Status of the maintenance
            $table->timestamps(); // Created at and updated at timestamps;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenances');
    }
};
