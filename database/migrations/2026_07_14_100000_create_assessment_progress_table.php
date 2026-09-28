<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentProgressTable extends Migration
{
    /**
     * In-progress mock-test state so a student can close the exam and resume
     * from the same question. One row per (user, assessment); cleared on submit.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_progress', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->integer('assessment_id')->index();
            $table->longText('answers')->nullable();   // JSON: { questionId: [optionId,...] }
            $table->longText('marked')->nullable();     // JSON: [questionId,...] marked for review
            $table->integer('current_index')->default(0);
            $table->integer('time_taken_sec')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'assessment_id']);
        });
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assessment_progress');
    }
}
