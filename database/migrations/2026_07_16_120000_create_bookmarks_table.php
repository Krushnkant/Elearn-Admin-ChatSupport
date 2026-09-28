<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Questions a learner has bookmarked from the exam runner, listed back on the
 * "My Bookmarks" page. One row per (user, question) — the unique index makes
 * the toggle idempotent.
 */
class CreateBookmarksTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('bookmarks')) {
            return;
        }
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->integer('question_id')->index();
            $table->timestamps();
            $table->unique(['user_id', 'question_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookmarks');
    }
}
