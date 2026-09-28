<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMockTestFormatFieldsToQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'external_id')) {
                $table->string('external_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('questions', 'topic_category')) {
                $table->string('topic_category')->nullable()->after('cognitive_level');
            }
            if (!Schema::hasColumn('questions', 'tested_skill')) {
                $table->string('tested_skill')->nullable()->after('topic_category');
            }
            if (!Schema::hasColumn('questions', 'question_style')) {
                $table->string('question_style')->nullable()->after('question_type');
            }
            if (!Schema::hasColumn('questions', 'last_reviewed')) {
                $table->date('last_reviewed')->nullable()->after('eco_task');
            }
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
            $table->dropColumn(['external_id', 'topic_category', 'tested_skill', 'question_style', 'last_reviewed']);
        });
    }
}
