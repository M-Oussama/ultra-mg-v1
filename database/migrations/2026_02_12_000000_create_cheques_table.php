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
        Schema::create('cheques', function (Blueprint $header) {
            $header->id();
            $header->date('cheque_date');
            $header->string('cheque_number');
            $header->foreignId('client_id')->constrained('certify_clients')->onDelete('cascade');
            $header->string('file_path')->nullable();
            $header->timestamps();
            $header->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheques');
    }
};
