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
        // Add component_key column first
        Schema::table('stationery_component_stocks', function (Blueprint $table) {
            $table->string('component_key')->nullable()->after('id');
        });

        // Populate component_key from existing component_id by joining components
        $rows = DB::table('stationery_component_stocks')->select('id', 'component_id')->get();
        foreach ($rows as $row) {
            if ($row->component_id) {
                $comp = DB::table('components')->where('id', $row->component_id)->first();
                if ($comp) {
                    $subject = str_pad($comp->subject_code, 4, '0', STR_PAD_LEFT);
                    $component_code = str_pad($comp->component_code, 2, '0', STR_PAD_LEFT);
                    $key = $subject . '-' . $component_code;
                    DB::table('stationery_component_stocks')->where('id', $row->id)->update(['component_key' => $key]);
                }
            }
        }

        // Drop foreign key and component_id column, then recreate unique index on component_key
        Schema::table('stationery_component_stocks', function (Blueprint $table) {
            // drop foreign key if exists
            try {
                $table->dropForeign(['component_id']);
            } catch (\Throwable $e) {
                // ignore if not exists
            }

            // drop the old column
            if (Schema::hasColumn('stationery_component_stocks', 'component_id')) {
                $table->dropColumn('component_id');
            }

            // adjust unique index
            try {
                $table->dropUnique('component_stock_unique');
            } catch (\Throwable $e) {
                // ignore
            }

            $table->unique(['component_key', 'stock_item_id'], 'component_stock_unique_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stationery_component_stocks', function (Blueprint $table) {
            // drop the new unique
            try {
                $table->dropUnique('component_stock_unique_key');
            } catch (\Throwable $e) {}

            // add component_id back as nullable (no FK restoration here)
            if (!Schema::hasColumn('stationery_component_stocks', 'component_id')) {
                $table->unsignedBigInteger('component_id')->nullable()->after('id');
            }

            // attempt to repopulate component_id from key
            $rows = DB::table('stationery_component_stocks')->select('id', 'component_key')->get();
            foreach ($rows as $row) {
                if ($row->component_key) {
                    $parts = explode('-', $row->component_key);
                    $subject = ltrim($parts[0] ?? '', '0');
                    $component_code = ltrim($parts[1] ?? '', '0');
                    $comp = DB::table('components')
                        ->where('subject_code', $subject)
                        ->where('component_code', $component_code)
                        ->first();
                    if ($comp) {
                        DB::table('stationery_component_stocks')->where('id', $row->id)->update(['component_id' => $comp->id]);
                    }
                }
            }

            // finally drop component_key
            if (Schema::hasColumn('stationery_component_stocks', 'component_key')) {
                $table->dropColumn('component_key');
            }
        });
    }
};
