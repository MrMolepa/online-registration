<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCenterCandidateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('center_candidate', function (Blueprint $table) {
            $table->id();
            $table->integer('candidate_no');
            $table->string('email');
            $table->integer('type');
            $table->string('session');
            $table->string('sponser');
            $table->string('center_no');
            $table->integer('subject_number');
            $table->timestamps();
        });
    }

   

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('center_candidate');
    }
}
