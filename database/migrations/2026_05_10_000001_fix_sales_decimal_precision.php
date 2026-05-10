<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fix "Numeric value out of range" (SQLSTATE 22003) on `sales` and `purchases` tables.
 *
 * Root cause: `balance` and `regulation` were defined as decimal() with no
 * explicit precision, which defaults to decimal(8,2) — max value 999,999.99.
 * Large amounts (e.g. 1,127,126 DZD) overflow this limit.
 *
 * Fix: Change all affected columns to decimal(15,2) — max value 9,999,999,999,999.99.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Fix sales table ───────────────────────────────────────────────────
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('balance', 15, 2)->default(0)->change();
            $table->decimal('regulation', 15, 2)->default(0)->change();
        });

        // ── Fix purchases table (same narrow-decimal bug) ─────────────────────
        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('balance', 15, 2)->default(0)->change();
            $table->decimal('regulation', 15, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('balance', 8, 2)->default(0)->change();
            $table->decimal('regulation', 8, 2)->default(0)->change();
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('balance', 8, 2)->default(0)->change();
            $table->decimal('regulation', 8, 2)->default(0)->change();
        });
    }
};
