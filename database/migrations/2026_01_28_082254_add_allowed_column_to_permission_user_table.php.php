<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('permission_user', function (Blueprint $table) {
            // Add allowed column if it doesn't exist
            if (!Schema::hasColumn('permission_user', 'allowed')) {
                $table->boolean('allowed')->default(true)->after('permission_id');
            }
        });
    }

    public function down()
    {
        Schema::table('permission_user', function (Blueprint $table) {
            if (Schema::hasColumn('permission_user', 'allowed')) {
                $table->dropColumn('allowed');
            }
        });
    }
};