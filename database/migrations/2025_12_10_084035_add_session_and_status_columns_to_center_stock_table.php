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
        Schema::table('stationery_center_stock', function (Blueprint $table) {
            // Add session_id column
            $table->unsignedBigInteger('session_id')->nullable()->after('component_id');
            
            // Add foreign key constraint
            $table->foreign('session_id')
                  ->references('id')
                  ->on('sessions')
                  ->onDelete('cascade');
            
            // Add allocation_breakdown column if it doesn't exist
            if (!Schema::hasColumn('stationery_center_stock', 'allocation_breakdown')) {
                $table->json('allocation_breakdown')->nullable()->after('quantity_dispatched');
            }
            
            // Add status column if it doesn't exist
            if (!Schema::hasColumn('stationery_center_stock', 'status')) {
                $table->string('status')->default('pending')->after('allocation_breakdown');
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
        Schema::table('stationery_center_stock', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
            $table->dropColumn(['session_id', 'allocation_breakdown', 'status']);
        });
    }
};