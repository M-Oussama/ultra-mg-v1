<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->time('transaction_time')->nullable()->after('transaction_date');
            $table->string('import_source')->nullable()->after('transaction_time')->index();
            $table->string('source_file_name')->nullable()->after('import_source');
            $table->unsignedInteger('source_row_number')->nullable()->after('source_file_name');
            $table->json('source_payload')->nullable()->after('source_row_number');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'transaction_time',
                'import_source',
                'source_file_name',
                'source_row_number',
                'source_payload',
            ]);
        });
    }
};
