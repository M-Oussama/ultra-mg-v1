<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('entrepreneur_settlements')
            ->select(['id', 'company_id', 'entrepreneur_name'])
            ->whereNotNull('entrepreneur_name')
            ->where('entrepreneur_name', '<>', '')
            ->orderBy('id')
            ->get()
            ->each(function ($settlement): void {
                $name = trim((string) $settlement->entrepreneur_name);
                if ($name === '') {
                    return;
                }

                $entrepreneurId = DB::table('settlement_entrepreneurs')
                    ->where('company_id', $settlement->company_id)
                    ->where('name', $name)
                    ->value('id');

                if ($entrepreneurId === null) {
                    $entrepreneurId = DB::table('settlement_entrepreneurs')->insertGetId([
                        'company_id' => $settlement->company_id,
                        'name' => $name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('entrepreneur_settlements')
                    ->where('id', $settlement->id)
                    ->update(['entrepreneur_id' => $entrepreneurId]);
            });
    }

    public function down(): void
    {
        // Keep the directory records; they may have been edited after the backfill.
    }
};
