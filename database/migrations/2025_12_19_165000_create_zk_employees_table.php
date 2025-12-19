<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('zk_employees', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary(); // Using ZK PIN as ID
            $table->unsignedBigInteger('zk_id'); // Using ZK PIN as ID
            $table->string('name')->nullable();
            $table->string('surname')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zk_employees');
    }
};
