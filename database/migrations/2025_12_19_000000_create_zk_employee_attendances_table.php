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
        if (!Schema::hasTable('zk_employee_attendances')) {
            Schema::create('zk_employee_attendances', function (Blueprint $table) {
                $table->id();
                $table->string('employee_id', 50)->comment('PIN from ZK device');
                $table->unsignedBigInteger('user_id')->nullable()->comment('Linked internal user id if found');
                $table->dateTime('punched_at')->nullable();
                $table->integer('type')->nullable()->comment('0: check-in, 1: check-out, etc');
                $table->text('raw_line')->nullable();
                $table->timestamps();

                $table->index('employee_id');
                $table->index('punched_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zk_employee_attendances');
    }
};
