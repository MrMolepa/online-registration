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
        Schema::create('stationery_stock_items', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to stock types
            $table->foreignId('stock_type_id')
                ->constrained('stationery_stock_types')
                ->onDelete('cascade');
            
            // Item details
            $table->string('name');
            $table->string('unit', 50); // e.g., pack, box, piece, ream
            $table->decimal('stock_qty', 10, 2)->default(0);
            
            // Optional supplier and inventory tracking
            $table->string('supplier_info')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            
            // Timestamps
            $table->timestamps();
            
            // Indexes for performance
            $table->index('stock_type_id');
            $table->index('name');
            $table->index('is_active');
            $table->index(['stock_type_id', 'is_active']); // Composite index for filtering
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stationery_stock_items');
    }
};