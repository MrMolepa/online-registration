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
        Schema::create('frontdesk_postal_receives', function (Blueprint $table) {
            $table->id();
            $table->string('package_name');
            $table->string('from_title');
            $table->string('to_title');
            $table->string('reference_number')->unique();
            $table->date('date_received');
            $table->timestamps();

            // Indexes for better performance
            $table->index('reference_number');
            $table->index('date_received');
            $table->index('from_title');
            $table->index('to_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frontdesk_postal_receives');
    }
};