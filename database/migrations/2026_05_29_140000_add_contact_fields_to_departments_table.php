<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('departments', function (Blueprint $table) {
            if (!Schema::hasColumn('departments', 'address')) {
                $table->string('address')->nullable()->after('name');
            }
            if (!Schema::hasColumn('departments', 'phone')) {
                $table->string('phone')->nullable()->after('address');
            }
            if (!Schema::hasColumn('departments', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('departments', 'logo_url')) {
                $table->string('logo_url')->nullable()->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('departments', function (Blueprint $table) {
            if (Schema::hasColumn('departments', 'logo_url')) {
                $table->dropColumn('logo_url');
            }
            if (Schema::hasColumn('departments', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('departments', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('departments', 'address')) {
                $table->dropColumn('address');
            }
        });
    }
};

