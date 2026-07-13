<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudyMaterialSeeder extends Seeder
{
    /**
     * Seed the 7 study-material books that were previously hardcoded in the
     * frontend PageController, so the page renders identically.
     */
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $books = [
            ['PMBOK® Guide 7th Edition',              'The definitive guide to the project management body of knowledge.', 'bi-journal-bookmark-fill', 'ic-blue'],
            ['Agile Practice Guide',                  'Understand agile principles and how to apply them to projects.',    'bi-person-workspace',      'ic-green'],
            ['Process Group Practice Guide',          'In-depth coverage of process groups and knowledge areas.',          'bi-diagram-3',             'ic-purple'],
            ['PMP® Exam Preparation',                 'Exam tips, strategies and key concepts for PMP® success.',          'bi-mortarboard',           'ic-amber'],
            ['Project Manager Competency Development','Build the skills and competencies of a successful PM.',             'bi-clipboard-data',        'ic-pink'],
            ['Stakeholder Engagement Guide',          'Learn effective stakeholder engagement strategies and techniques.', 'bi-people',                'ic-teal'],
            ['Risk Management Practice Guide',        'Identify, assess and manage project risks like a pro.',             'bi-shield-exclamation',    'ic-orange'],
        ];

        foreach ($books as $i => $b) {
            DB::table('study_materials')->updateOrInsert(
                ['title' => $b[0]],
                [
                    'description' => $b[1],
                    'icon'        => $b[2],
                    'color'       => $b[3],
                    'sort_order'  => $i + 1,
                    'status'      => 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]
            );
        }
    }
}
