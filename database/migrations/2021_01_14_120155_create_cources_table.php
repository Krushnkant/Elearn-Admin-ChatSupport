<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cources', function (Blueprint $table) {
            $table->id();
            //$table->integer('question_id');
            $table->longText('name');
            $table->string('duration')->nullable();
            $table->integer('skill_id')->nullable();
            $table->string('trainnig_mode')->default(1)->comment('1 For Flexible');
            $table->text('overview')->nullable();
            $table->text('mock_test')->nullable();
            $table->text('course_outline')->nullable();
            $table->integer('mock_exam_count')->nullable();
            $table->index(['name']);
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
        Schema::dropIfExists('cources');
    }
}
