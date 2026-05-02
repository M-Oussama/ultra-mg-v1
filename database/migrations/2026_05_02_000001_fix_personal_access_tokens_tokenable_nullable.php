<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Fix: on production, the original personal_access_tokens migration created
 * a `tokenable` column as NOT NULL without a default. Sanctum's createToken()
 * writes to `tokenable_type`+`tokenable_id` only, causing a 1364 error.
 *
 * The local DB was already re-migrated without this column, so we guard with
 * Schema::hasColumn() to make this migration safe in both environments.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('personal_access_tokens', 'tokenable')) {
            // Use RAW SQL to rename because renameColumn() might require doctrine/dbal
            DB::statement(
                'ALTER TABLE personal_access_tokens CHANGE `tokenable` `tokenable_legacy` VARCHAR(255) NULL DEFAULT NULL'
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('personal_access_tokens', 'tokenable_legacy')) {
            Schema::table('personal_access_tokens', function ($table) {
                $table->renameColumn('tokenable_legacy', 'tokenable');
            });
        }
    }
};
