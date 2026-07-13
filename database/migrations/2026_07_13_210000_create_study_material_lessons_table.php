<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudyMaterialLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('study_material_lessons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('study_material_id')->index();
            $table->string('title');
            $table->longText('content')->nullable(); // rich HTML
            $table->string('file')->nullable();       // uploaded PDF filename
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(1)->comment('0 For Inactive, 1 For Active');
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
        Schema::dropIfExists('study_material_lessons');
    }
}
