<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiveVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('live_videos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('day_no')->default(1);
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('video_count')->default(0);
            $table->integer('duration_mins')->default(0);
            $table->string('video_url')->nullable();
            $table->dateTime('scheduled_at')->nullable();
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
        Schema::dropIfExists('live_videos');
    }
}
