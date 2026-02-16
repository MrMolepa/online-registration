<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFunWalkRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fun_walk_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fun_walk_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('email');
            $table->string('phone');
            $table->string('ticket_number')->unique();
            $table->string('qr_path')->nullable();
            $table->timestamps();
            
            // Foreign key
            $table->foreign('fun_walk_id')->references('id')->on('fun_walks')->onDelete('cascade');
            
            // Indexes
            $table->index('fun_walk_id');
            $table->index('ticket_number');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fun_walk_registrations');
    }
}
