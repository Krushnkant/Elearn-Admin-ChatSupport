<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Question set for a custom (builder-generated) assessment.
 *
 * Curated tests own their questions through questions.assessment_id, which ties
 * each question row to exactly one test. A custom test draws from the whole pool
 * instead, so it needs its own link table rather than re-parenting questions.
 */
class CreateAssessmentQuestionsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('assessment_questions')) {
            return;
        }
        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('assessment_id')->index();
            $table->integer('question_id')->index();
            $table->integer('position')->default(0);   // presentation order
            $table->unique(['assessment_id', 'question_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('assessment_questions');
    }
}
