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
        Schema::create('frontdesk_phone_call_logs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->date('date')->nullable();
            $table->text('description')->nullable();
            $table->date('next_follow_up_date')->nullable();
            $table->string('call_duration')->nullable(); // Example: "5m 30s" or "00:05:30"
            $table->text('note')->nullable();
            $table->string('call_type')->nullable(); // e.g. "incoming", "outgoing", "missed"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frontdesk_phone_call_logs');
    }
};