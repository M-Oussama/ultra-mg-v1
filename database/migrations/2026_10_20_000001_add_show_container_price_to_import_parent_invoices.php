<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('import_parent_invoices', function (Blueprint $table) {
            $table->boolean('show_container_price')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('import_parent_invoices', function (Blueprint $table) {
            $table->dropColumn('show_container_price');
        });
    }
};
