<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LiveVideoSeeder extends Seeder
{
    /**
     * Seed the 6 PMP live-video sessions that were previously hardcoded
     * in the frontend liveVideos page, so the page renders identically.
     */
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $sessions = [
            'Introduction to PMP® & Project Management Framework',
            'Project Integration Management',
            'Project Scope Management',
            'Project Schedule Management',
            'Project Cost & Quality Management',
            'Project Resource, Communication, Risk & Procurement Management',
        ];

        foreach ($sessions as $i => $title) {
            DB::table('live_videos')->updateOrInsert(
                ['day_no' => $i + 1, 'title' => $title],
                [
                    'description'   => null,
                    'video_count'   => 6,
                    'duration_mins' => 120,
                    'video_url'     => null,
                    'scheduled_at'  => null,
                    'status'        => 1,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]
            );
        }
    }
}
