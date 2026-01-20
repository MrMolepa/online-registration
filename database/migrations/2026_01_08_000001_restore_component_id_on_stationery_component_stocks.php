<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stationery_component_stocks', function (Blueprint $table) {
            if (!Schema::hasColumn('stationery_component_stocks', 'component_id')) {
                $table->foreignId('component_id')->nullable()->after('id')->constrained('components')->onDelete('cascade');
            }
        });

        // Populate component_id from component_key
        $rows = DB::table('stationery_component_stocks')->select('id', 'component_key')->get();
        foreach ($rows as $row) {
            if ($row->component_key) {
                $parts = explode('-', $row->component_key);
                if (count($parts) >= 2) {
                    $subject = ltrim($parts[0], '0');
                    $compCode = ltrim($parts[1], '0');
                    $comp = DB::table('components')
                        ->where('subject_code', $subject)
                        ->where('component_code', $compCode)
                        ->first();
                    if ($comp) {
                        DB::table('stationery_component_stocks')->where('id', $row->id)->update(['component_id' => $comp->id]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stationery_component_stocks', function (Blueprint $table) {
            try {
                $table->dropForeign(['component_id']);
            } catch (\Throwable $e) {}
            if (Schema::hasColumn('stationery_component_stocks', 'component_id')) {
                $table->dropColumn('component_id');
            }
        });
    }
};
