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
        Schema::create('z_k_attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->date('date'); // The date of attendance

            $table->timestamp('check_in')->nullable();     // First punch
            $table->timestamp('check_out')->nullable();    // Last punch

            $table->integer('worked_minutes')->nullable(); // Total duration in minutes

            $table->enum('status', ['complete', 'incomplete', 'auto_closed'])->default('incomplete');

            $table->string('note')->nullable();            // Optional: "Auto check-out", "Manual edit", etc.

            $table->timestamps(); // Laravel's created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('z_k_attendances');
    }
};
