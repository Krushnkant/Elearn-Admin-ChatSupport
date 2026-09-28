<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateContentSettingsTable extends Migration
{
    /**
     * Key/value store for app & website copy edited from the admin panel's
     * "Content Management" section. Keys are namespaced by screen, e.g.
     * `mock_test.title`, so more screens can be added without new tables.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        $defaults = [
            'mock_test.title'       => 'PMP® Mock Test',
            'mock_test.badge_1'     => '10 Full-Length Tests',
            'mock_test.badge_2'     => 'PMBOK® 8 Based',
            'mock_test.badge_3'     => 'Exam Simulation',
            'mock_test.badge_4'     => 'Detailed Solutions',
            'mock_test.description' => 'Practice with 10 full-length mock tests designed as per the PMBOK® 8 Examination Content Outline to help you assess your readiness and improve your score.',
        ];

        foreach ($defaults as $key => $value) {
            DB::table('content_settings')->insert([
                'key'        => $key,
                'value'      => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('content_settings');
    }
}
