<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up(): void
    {
        Schema::create('menu_permissions', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('menu_id'); // Foreign key to menus
            $table->unsignedBigInteger('role_id'); // Foreign key to roles
            $table->unsignedBigInteger('permission_id'); // Foreign key to permissions
            $table->string('guard_name'); // Guard assignment
            

            // Foreign key constraints
            $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_permissions');
    }
}
