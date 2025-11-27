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
        Schema::create('stationery_center_stock', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->string('center_id')->nullable();
            $table->foreignId('stock_item_id')->constrained('stationery_stock_items')->onDelete('cascade');
            $table->foreignId('component_id')->nullable()->constrained('components')->onDelete('set null');
            
            // Quantity Fields
            $table->decimal('quantity_allocated', 10, 2)->default(0.00);
            $table->decimal('quantity_dispatched', 10, 2)->default(0.00);
            
            // Date Fields
            $table->date('dispatch_date')->nullable();
            $table->date('received_date')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('center_id');
            $table->index('stock_item_id');
            $table->index('component_id');
            $table->index('dispatch_date');
            $table->index('received_date');
            
            // Composite index for common queries
            $table->index(['center_id', 'stock_item_id', 'component_id'], 'center_stock_composite_idx');
            
            $table->foreign('center_id')->references('center_no')->on('centers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {  
        Schema::dropIfExists('stationery_center_stock');
    }
};