<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id');
            $table->integer('sub_category_id')->nullable();
            $table->integer('course_id')->nullable();
            $table->longText('title');
            $table->longText('explanation');
            $table->tinyInteger('question_type')->default(1)->comment('1 For Single, 2 For Multiple');
            $table->integer('marks');
            $table->string('dificulty_level')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0 For Inactive, 1 For Active');
            $table->integer('assessment_id')->nullable();
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
        Schema::table('questions', function (Blueprint $table) {
            //$table->dropColumn('status');
        });
    }
}
