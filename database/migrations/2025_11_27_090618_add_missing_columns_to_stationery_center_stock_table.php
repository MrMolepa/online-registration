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
        // Check if columns need to be added
        Schema::table('stationery_center_stock', function (Blueprint $table) {
            // Add session_id if it doesn't exist
            if (!Schema::hasColumn('stationery_center_stock', 'session_id')) {
                $table->unsignedBigInteger('session_id')->after('component_id');
                $table->foreign('session_id')
                      ->references('id')
                      ->on('sessions')
                      ->onDelete('cascade');
            }
            
            // Add allocation_breakdown if it doesn't exist
            if (!Schema::hasColumn('stationery_center_stock', 'allocation_breakdown')) {
                $table->json('allocation_breakdown')->nullable()->after('quantity_dispatched');
            }
            
            // Add status if it doesn't exist
            if (!Schema::hasColumn('stationery_center_stock', 'status')) {
                $table->enum('status', ['pending', 'allocated', 'dispatched', 'received', 'cancelled'])
                      ->default('pending')
                      ->after('allocation_breakdown');
            }
            
            // Add indexes if they don't exist
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexesFound = $sm->listTableIndexes('stationery_center_stock');
            
            if (!isset($indexesFound['stationery_center_stock_center_id_index'])) {
                $table->index('center_id');
            }
            
            if (!isset($indexesFound['stationery_center_stock_session_id_index'])) {
                $table->index('session_id');
            }
            
            if (!isset($indexesFound['stationery_center_stock_status_index'])) {
                $table->index('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stationery_center_stock', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['session_id']);
            
            // Drop columns
            $table->dropColumn([
                'session_id',
                'allocation_breakdown',
                'status'
            ]);
            
            // Drop indexes
            $table->dropIndex(['center_id']);
            $table->dropIndex(['session_id']);
            $table->dropIndex(['status']);
        });
    }
};