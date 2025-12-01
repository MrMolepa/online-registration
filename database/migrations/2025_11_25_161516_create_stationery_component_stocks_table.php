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
        Schema::create('stationery_component_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('component_id')->constrained('components')->onDelete('cascade');
            $table->foreignId('stock_item_id')->constrained('stationery_stock_items')->onDelete('cascade');
            $table->enum('rule_type', ['per_candidate', 'per_center', 'per_invigilator', 'fixed', 'conditional'])
                  ->default('per_candidate');
            $table->decimal('base_qty', 10, 2)->default(1.00);
            $table->decimal('multiplier', 10, 4)->default(1.0000);
            $table->integer('extras_fixed')->default(0);
            $table->decimal('extras_per_candidate', 10, 4)->default(0.0000);
            $table->decimal('extras_percentage', 5, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Unique constraint: one rule per component-stock_item combination
            $table->unique(['component_id', 'stock_item_id'], 'component_stock_unique');
            
            // Indexes for better performance
            $table->index('component_id');
            $table->index('stock_item_id');
            $table->index('rule_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stationery_component_stocks');
    }
};