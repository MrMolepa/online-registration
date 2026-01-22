<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubjectGroupSubjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subject_group_subject', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subject_group_id');
            $table->unsignedInteger('subject_code');
            $table->timestamps();

            $table->foreign('subject_group_id')->references('id')->on('subject_groups')->onDelete('cascade');
            $table->foreign('subject_code')->references('subject_code')->on('subjects')->onDelete('cascade');
            
            $table->unique(['subject_group_id', 'subject_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subject_group_subject');
    }
}