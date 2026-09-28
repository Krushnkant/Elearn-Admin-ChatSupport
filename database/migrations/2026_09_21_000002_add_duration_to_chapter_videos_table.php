<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDurationToChapterVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chapter_videos', function (Blueprint $table) {
            $table->string('duration', 20)->nullable()->after('video'); // e.g. "12:34"
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chapter_videos', function (Blueprint $table) {
            $table->dropColumn('duration');
        });
    }
}
