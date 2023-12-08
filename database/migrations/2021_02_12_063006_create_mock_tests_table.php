<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMockTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->integer('course_id');
            $table->string('title');
            $table->tinyInteger('status')->default(0)->comment('0 For Inactive, 1 For Active');
            $table->integer('number_of_questions');
            $table->integer('skill_id');
            $table->string('mock_exam', 100);
            $table->index(['title']);
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
        Schema::dropIfExists('assessments');
    }
}
