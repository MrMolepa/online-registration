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
        Schema::table('stationery_center_stock', function (Blueprint $table) {
            if (!Schema::hasColumn('stationery_center_stock', 'num_candidates')) {
                $table->integer('num_candidates')->nullable()->default(0)->after('quantity_allocated');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stationery_center_stock', function (Blueprint $table) {
            if (Schema::hasColumn('stationery_center_stock', 'num_candidates')) {
                $table->dropColumn('num_candidates');
            }
        });
    }
};
