<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiveVideoLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('live_video_links', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('live_video_id')->index();
            $table->string('title');
            $table->string('video_url')->nullable();
            $table->integer('duration_mins')->default(0);
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
        Schema::dropIfExists('live_video_links');
    }
}
