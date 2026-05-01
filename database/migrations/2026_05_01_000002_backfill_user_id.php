<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Employee;
use App\Models\Client;
use App\Models\Sale;
use App\Models\ImportationInvoice;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Recovery: Ensure columns exist before backfilling
        $tables = ['sales', 'clients', 'employees', 'importation_invoices'];
        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'user_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                });
            }
        }

        // 2. Backfill Logic
        // Find the first admin or first user to own orphan records
        $admin = User::whereHas('role', function($q) {
            $q->where('name', 'Admin')->orWhere('role', 'Admin')->orWhere('role', 'admin');
        })->first();

        $adminId = $admin ? $admin->id : 1;

        Employee::whereNull('user_id')->update(['user_id' => $adminId]);
        Client::whereNull('user_id')->update(['user_id' => $adminId]);
        Sale::whereNull('user_id')->update(['user_id' => $adminId]);
        
        if (Schema::hasTable('importation_invoices')) {
            ImportationInvoice::whereNull('user_id')->update(['user_id' => $adminId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to undo backfill
    }
};
