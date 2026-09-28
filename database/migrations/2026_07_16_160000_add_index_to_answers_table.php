<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToAnswersTable extends Migration
{
    /**
     * answers had no index besides the PK, so every per-test lookup
     * (reports, review, PDF download, exam scoring) full-scanned ~390k rows.
     * The composite also covers WHERE mock_test_id = ? alone (leftmost prefix).
     *
     * @return void
     */
    public function up()
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->index(['mock_test_id', 'question_id'], 'answers_mock_test_question_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->dropIndex('answers_mock_test_question_idx');
        });
    }
}
