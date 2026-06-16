<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('fcm_token')->nullable()->after('remember_token')->index();
            $table->string('fcm_platform')->nullable()->after('fcm_token');
            $table->string('fcm_device_name')->nullable()->after('fcm_platform');
            $table->timestamp('fcm_token_updated_at')->nullable()->after('fcm_device_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'fcm_token',
                'fcm_platform',
                'fcm_device_name',
                'fcm_token_updated_at',
            ]);
        });
    }
};
