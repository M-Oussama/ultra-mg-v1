<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('stockable')->default(true)->change();
            });

            return;
        }

        DB::statement('ALTER TABLE products ALTER COLUMN stockable SET DEFAULT 1');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('stockable')->default(false)->change();
            });

            return;
        }

        DB::statement('ALTER TABLE products ALTER COLUMN stockable SET DEFAULT 0');
    }
};
