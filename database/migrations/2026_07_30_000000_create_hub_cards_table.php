<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateHubCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hub_cards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon')->default('bi-journal-bookmark-fill');
            $table->string('color')->default('ic-blue');
            $table->string('link_url')->nullable()->comment('Frontend path, e.g. /liveVideos. Empty = "Coming soon" card.');
            $table->tinyInteger('is_new')->default(0)->comment('0 = no badge, 1 = show NEW badge');
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(1)->comment('0 For Inactive, 1 For Active');
            $table->timestamps();
        });

        DB::table('hub_cards')->insert([
            [
                'title'       => 'PMP® Live Videos',
                'description' => 'Watch expert-led video lectures to understand concepts better.',
                'icon'        => 'bi-play-circle',
                'color'       => 'ic-amber',
                'link_url'    => '/liveVideos',
                'is_new'      => 0,
                'sort_order'  => 10,
                'status'      => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'PMP® Study Material',
                'description' => 'Access comprehensive study material aligned to PMBOK® Guide.',
                'icon'        => 'bi-file-earmark-text',
                'color'       => 'ic-blue',
                'link_url'    => '/studyMaterial',
                'is_new'      => 0,
                'sort_order'  => 20,
                'status'      => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'PMP® Mock Test',
                'description' => 'Take full-length mock tests and practice exam-style questions.',
                'icon'        => 'bi-clipboard-check',
                'color'       => 'ic-green',
                'link_url'    => '/mockTest',
                'is_new'      => 0,
                'sort_order'  => 30,
                'status'      => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Mock Test Builder',
                'description' => 'Create custom mock tests based on your preferences.',
                'icon'        => 'bi-grid-1x2',
                'color'       => 'ic-purple',
                'link_url'    => '/mockTestBuilder',
                'is_new'      => 1,
                'sort_order'  => 40,
                'status'      => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'PMP® Glossary',
                'description' => 'Explore key terms and definitions to strengthen your concepts.',
                'icon'        => 'bi-fonts',
                'color'       => 'ic-orange',
                'link_url'    => null,
                'is_new'      => 0,
                'sort_order'  => 50,
                'status'      => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'PMP® Formulas',
                'description' => 'Quick reference to important formulas and calculations.',
                'icon'        => 'bi-calculator',
                'color'       => 'ic-pink',
                'link_url'    => null,
                'is_new'      => 0,
                'sort_order'  => 60,
                'status'      => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Get in Touch',
                'description' => 'Have questions? Our team is here to help you succeed.',
                'icon'        => 'bi-headset',
                'color'       => 'ic-teal',
                'link_url'    => '/getInTouch',
                'is_new'      => 0,
                'sort_order'  => 70,
                'status'      => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'PMP® Exam Tips',
                'description' => 'Expert tips, strategies and best practices to ace the exam.',
                'icon'        => 'bi-lightbulb',
                'color'       => 'ic-green',
                'link_url'    => null,
                'is_new'      => 0,
                'sort_order'  => 80,
                'status'      => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hub_cards');
    }
}
