<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubjectGroupRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subject_group_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('level_id');
            $table->string('financial_year', 20);
            $table->integer('type')->comment('Candidate registration type: 1=Full, 2=Partial, 3=Private');
            $table->json('rules')->comment('JSON structure: {required_groups: [], forbidden_groups: [], min_subjects: int, max_subjects: int, group_constraints: []}');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('level_id')->references('id')->on('levels')->onDelete('cascade');
            $table->index(['level_id', 'financial_year', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subject_group_rules');
    }
}