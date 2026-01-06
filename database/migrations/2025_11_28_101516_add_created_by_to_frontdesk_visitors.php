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
        Schema::table('frontdesk_visitors', function (Blueprint $table) {
            $table->string('created_by')->nullable()->after('out_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frontdesk_visitors', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
    }
};