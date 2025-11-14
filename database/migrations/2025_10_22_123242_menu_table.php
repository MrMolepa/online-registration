<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id()->unsignedBigInteger(); // Primary key
            $table->string('name');                   // Menu text
            $table->string('route')->nullable();       // Laravel route name
            $table->string('icon')->nullable();        // e.g. "fas fa-home"
            $table->string('role')->nullable();        // e.g. "admin", "teacher"
            $table->string('permission')->nullable();  // optional permission name
            $table->unsignedBigInteger('parent_id')->nullable(); // for nesting
            $table->integer('order')->default(0); 
            $table->boolean('is_active')->default(true);    // for sorting
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
}
