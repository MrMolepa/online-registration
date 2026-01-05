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
            $table->id();  // FIXED

            $table->string('name');
            $table->string('route')->nullable();
            $table->string('icon')->nullable();
            $table->string('role')->nullable();
            $table->string('permission')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('order')->default(0);
            $table->string('guard_name')->default('web');
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
}
