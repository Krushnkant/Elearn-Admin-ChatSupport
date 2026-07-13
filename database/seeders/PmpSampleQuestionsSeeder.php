<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PmpSampleQuestionsSeeder extends Seeder
{
    /**
     * Seeds a clean sample mock test with 10 realistic PMP questions, each with
     * full rich metadata + per-option why-wrong, so the whole mock-test flow
     * (exam -> score report -> review) shows proper content.
     *
     * Domains (type 1): People=1, Process=2, Business Environment=3
     * Knowledge (type 2): Delivery=25, Dev Approach=26, Measurement=27,
     *   Planning=28, Project Work=29, Stakeholder=30, Team=31, Uncertainty=32
     */
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $courseId = DB::table('cources')->min('id') ?: 1;

        // Dedicated, clean assessment.
        DB::table('assessments')->updateOrInsert(
            ['title' => 'PMP® Sample Mock Test (10 Q)'],
            [
                'course_id' => $courseId, 'status' => 1, 'number_of_questions' => 10,
                'duration_mins' => 20, 'skill_id' => DB::table('skills')->min('id') ?: 1,
                'mock_exam' => 'Sample', 'description' => 'A 10-question sample mock test with full solutions and PMP metadata.',
                'updated_at' => $now, 'created_at' => $now,
            ]
        );
        $assessmentId = DB::table('assessments')->where('title', 'PMP® Sample Mock Test (10 Q)')->value('id');

        // Wipe any previous sample questions for a clean re-seed.
        $oldIds = DB::table('questions')->where('assessment_id', $assessmentId)->pluck('id');
        if ($oldIds->count()) {
            DB::table('question_options')->whereIn('question_id', $oldIds)->delete();
            DB::table('category_questions')->whereIn('question_id', $oldIds)->delete();
            DB::table('questions')->whereIn('id', $oldIds)->delete();
        }

        foreach ($this->questions() as $i => $q) {
            $qid = DB::table('questions')->insertGetId([
                'set_type'        => 'set1',
                'category_id'     => 1,                 // stored type code (domain)
                'sub_category_id' => $q['domain'],      // used by review "Category" + report domains
                'categoryid'      => $q['knowledge'],
                'course_id'       => $courseId,
                'assessment_id'   => $assessmentId,
                'title'           => $q['title'],
                'question_type'   => 1,
                'marks'           => 1,
                'dificulty_level' => $q['difficulty'],
                'process_group'   => $q['process_group'],
                'methodology'     => $q['methodology'],
                'cognitive_level' => $q['cognitive_level'],
                'pmbok_ref'       => $q['pmbok_ref'],
                'agile_ref'       => $q['agile_ref'],
                'exam_tip'        => $q['exam_tip'],
                'exam_trap'       => $q['exam_trap'],
                'explanation'     => $q['explanation'],
                'status'          => 1,
                'created_at'      => $now, 'updated_at' => $now,
            ]);

            foreach ($q['options'] as $opt) {
                DB::table('question_options')->insert([
                    'question_id' => $qid,
                    'options'     => $opt['text'],
                    'is_correct'  => !empty($opt['correct']) ? 1 : 0,
                    'why_wrong'   => $opt['why'] ?? null,
                    'created_at'  => $now, 'updated_at' => $now,
                ]);
            }

            // Pivot rows so the score report's domain + topic breakdowns work.
            DB::table('category_questions')->insert([
                ['question_id' => $qid, 'set_type' => 'set1', 'category_id' => 1, 'sub_category_id' => $q['domain']],
                ['question_id' => $qid, 'set_type' => 'set1', 'category_id' => 2, 'sub_category_id' => $q['knowledge']],
            ]);
        }

        echo "Seeded assessment #{$assessmentId} with 10 sample questions.\n";
    }

    private function questions()
    {
        return [
            [
                'domain' => 1, 'knowledge' => 31, 'difficulty' => 'Easy', 'process_group' => 'Executing',
                'methodology' => 'Agile', 'cognitive_level' => 'Understand',
                'pmbok_ref' => 'Team Performance Domain', 'agile_ref' => 'Servant Leadership',
                'exam_tip' => 'A servant leader removes impediments rather than assigning blame.',
                'exam_trap' => 'Do not escalate to the functional manager as a first step.',
                'title' => 'A self-organizing Agile team member is repeatedly blocked by a slow external approval. As the Scrum Master, what should you do FIRST?',
                'explanation' => 'A servant-leader Scrum Master focuses on removing impediments for the team. Facilitating faster approval directly unblocks delivery.',
                'options' => [
                    ['text' => 'Work with the approver to streamline and remove the impediment.', 'correct' => true],
                    ['text' => 'Escalate the team member to their functional manager.', 'why' => 'Blaming the individual ignores the impediment and is not servant leadership.'],
                    ['text' => 'Add more buffer to every future sprint.', 'why' => 'Padding estimates hides the problem instead of solving it.'],
                    ['text' => 'Remove the dependency from the backlog.', 'why' => 'Dropping needed work to avoid an impediment does not deliver value.'],
                ],
            ],
            [
                'domain' => 1, 'knowledge' => 30, 'difficulty' => 'Easy', 'process_group' => 'Planning',
                'methodology' => 'Agile', 'cognitive_level' => 'Understand',
                'pmbok_ref' => 'Stakeholder Engagement', 'agile_ref' => 'User Stories, Personas',
                'exam_tip' => 'Think “customer value” whenever personas are mentioned.',
                'exam_trap' => 'Do not confuse personas with requirements documentation.',
                'title' => 'During backlog refinement, the Product Owner suggests creating personas before writing user stories. What is the PRIMARY benefit of personas?',
                'explanation' => 'Personas represent groups of users with similar goals, helping the team write stories that maximize customer value.',
                'options' => [
                    ['text' => 'They align user stories with the real needs and goals of end users.', 'correct' => true],
                    ['text' => 'They replace the need for detailed requirements documentation.', 'why' => 'Personas complement, not replace, requirements.'],
                    ['text' => 'They identify a specific customer to answer questions on demand.', 'why' => 'Personas are fictional representations, not real individuals.'],
                    ['text' => 'They guarantee each story fits within a single sprint.', 'why' => 'Personas do not determine story size or sprint capacity.'],
                ],
            ],
            [
                'domain' => 2, 'knowledge' => 28, 'difficulty' => 'Moderate', 'process_group' => 'Planning',
                'methodology' => 'Predictive', 'cognitive_level' => 'Apply',
                'pmbok_ref' => 'Schedule Management', 'agile_ref' => '',
                'exam_tip' => 'The critical path is the longest path and has zero total float.',
                'exam_trap' => 'The critical path is not always the path with the most activities.',
                'title' => 'A network diagram has paths of 14, 16, and 12 days. A non-critical activity on the 14-day path is delayed by 3 days. What happens to the project end date?',
                'explanation' => 'The 16-day path is critical. Delaying the 14-day path by 3 days makes it 17 days, which now exceeds 16, so the project is delayed by 1 day.',
                'options' => [
                    ['text' => 'The project is delayed by 1 day.', 'correct' => true],
                    ['text' => 'No impact; the activity was not on the critical path.', 'why' => 'The 3-day delay exceeds the path float, so it now drives the schedule.'],
                    ['text' => 'The project is delayed by 3 days.', 'why' => 'Only the amount beyond the float (1 day) affects the end date.'],
                    ['text' => 'The project finishes 2 days early.', 'why' => 'A delay cannot shorten the schedule.'],
                ],
            ],
            [
                'domain' => 2, 'knowledge' => 25, 'difficulty' => 'Moderate', 'process_group' => 'Monitoring & Controlling',
                'methodology' => 'Predictive', 'cognitive_level' => 'Apply',
                'pmbok_ref' => 'Change Control', 'agile_ref' => '',
                'exam_tip' => 'Evaluate the impact and take the change through integrated change control.',
                'exam_trap' => 'Never implement a change directly just because the sponsor asked.',
                'title' => 'Midway through execution, the sponsor requests a significant new feature. What should the project manager do FIRST?',
                'explanation' => 'Any change must be assessed for impact and processed through integrated change control before implementation.',
                'options' => [
                    ['text' => 'Assess the impact and submit a change request.', 'correct' => true],
                    ['text' => 'Implement the feature immediately to satisfy the sponsor.', 'why' => 'Implementing without impact analysis bypasses change control.'],
                    ['text' => 'Reject the request because the baseline is fixed.', 'why' => 'Changes are allowed; they must be evaluated, not refused outright.'],
                    ['text' => 'Add it to the next project without analysis.', 'why' => 'Deferring without assessment ignores the sponsor’s valid need.'],
                ],
            ],
            [
                'domain' => 2, 'knowledge' => 32, 'difficulty' => 'Difficult', 'process_group' => 'Planning',
                'methodology' => 'Predictive', 'cognitive_level' => 'Analyze',
                'pmbok_ref' => 'Risk Response Strategies', 'agile_ref' => '',
                'exam_tip' => 'Transfer shifts the impact/ownership of a threat to a third party (e.g., insurance).',
                'exam_trap' => 'Mitigate reduces probability/impact; it does not move ownership.',
                'title' => 'To handle a low-probability but high-cost threat, the team buys insurance to cover potential losses. Which risk response strategy is this?',
                'explanation' => 'Purchasing insurance transfers the financial impact and ownership of the threat to a third party.',
                'options' => [
                    ['text' => 'Transfer', 'correct' => true],
                    ['text' => 'Mitigate', 'why' => 'Mitigation reduces probability or impact but keeps ownership.'],
                    ['text' => 'Avoid', 'why' => 'Avoidance eliminates the threat, e.g., by changing the plan.'],
                    ['text' => 'Accept', 'why' => 'Acceptance takes no proactive action beyond a contingency.'],
                ],
            ],
            [
                'domain' => 3, 'knowledge' => 27, 'difficulty' => 'Moderate', 'process_group' => 'Monitoring & Controlling',
                'methodology' => 'Predictive', 'cognitive_level' => 'Apply',
                'pmbok_ref' => 'Earned Value Management', 'agile_ref' => '',
                'exam_tip' => 'CPI < 1 means over budget; SPI < 1 means behind schedule.',
                'exam_trap' => 'Do not confuse CPI (cost) with SPI (schedule).',
                'title' => 'A project reports EV = 800, AC = 1000, PV = 900. What does the cost performance indicate?',
                'explanation' => 'CPI = EV/AC = 800/1000 = 0.8, which is less than 1, so the project is over budget.',
                'options' => [
                    ['text' => 'Over budget (CPI = 0.8).', 'correct' => true],
                    ['text' => 'Under budget (CPI = 1.25).', 'why' => 'CPI is EV/AC = 0.8, not 1.25.'],
                    ['text' => 'On budget (CPI = 1.0).', 'why' => 'EV and AC are not equal, so CPI is not 1.'],
                    ['text' => 'Ahead of schedule (SPI = 1.1).', 'why' => 'The question asks about cost (CPI), and SPI = EV/PV = 0.89 anyway.'],
                ],
            ],
            [
                'domain' => 1, 'knowledge' => 31, 'difficulty' => 'Moderate', 'process_group' => 'Executing',
                'methodology' => 'Agile', 'cognitive_level' => 'Understand',
                'pmbok_ref' => 'Conflict Management', 'agile_ref' => 'Team Charter',
                'exam_tip' => 'Collaborate/Problem-solve is the best long-term conflict approach.',
                'exam_trap' => 'Forcing and smoothing are quick but rarely the “best” answer.',
                'title' => 'Two senior developers strongly disagree on an architecture decision, stalling the sprint. What is the BEST way to resolve the conflict?',
                'explanation' => 'Collaborating (problem-solving) brings differing views together to reach a consensus that both can support.',
                'options' => [
                    ['text' => 'Facilitate a discussion so both reach a shared, workable solution.', 'correct' => true],
                    ['text' => 'Decide for them to save time.', 'why' => 'Forcing a decision damages team ownership and morale.'],
                    ['text' => 'Ask them to avoid the topic and move on.', 'why' => 'Withdrawing leaves the conflict unresolved.'],
                    ['text' => 'Split the difference to appease both.', 'why' => 'Compromise is lose-lose and may pick a technically weaker option.'],
                ],
            ],
            [
                'domain' => 2, 'knowledge' => 28, 'difficulty' => 'Easy', 'process_group' => 'Planning',
                'methodology' => 'Predictive', 'cognitive_level' => 'Remember',
                'pmbok_ref' => 'Estimating Techniques', 'agile_ref' => '',
                'exam_tip' => 'Three-point (PERT) = (O + 4M + P) / 6.',
                'exam_trap' => 'Do not use a simple average (O+M+P)/3 for PERT.',
                'title' => 'Optimistic = 6, Most Likely = 9, Pessimistic = 18 days. What is the PERT (beta) expected duration?',
                'explanation' => 'PERT = (6 + 4×9 + 18) / 6 = (6 + 36 + 18)/6 = 60/6 = 10 days.',
                'options' => [
                    ['text' => '10 days', 'correct' => true],
                    ['text' => '11 days', 'why' => 'That is the simple average (6+9+18)/3 = 11, not PERT.'],
                    ['text' => '9 days', 'why' => 'That is the most likely value, not the weighted expected value.'],
                    ['text' => '12 days', 'why' => 'This does not match the PERT formula result of 10.'],
                ],
            ],
            [
                'domain' => 3, 'knowledge' => 29, 'difficulty' => 'Moderate', 'process_group' => 'Executing',
                'methodology' => 'Hybrid', 'cognitive_level' => 'Apply',
                'pmbok_ref' => 'Procurement Management', 'agile_ref' => '',
                'exam_tip' => 'For a fixed-price contract, the seller bears the cost risk.',
                'exam_trap' => 'Cost-plus contracts put more cost risk on the buyer.',
                'title' => 'A buyer wants to minimize their cost risk for well-defined work. Which contract type is MOST appropriate?',
                'explanation' => 'A firm fixed-price (FFP) contract places cost risk on the seller and suits well-defined scope.',
                'options' => [
                    ['text' => 'Firm Fixed-Price (FFP)', 'correct' => true],
                    ['text' => 'Cost Plus Fixed Fee (CPFF)', 'why' => 'Cost-plus shifts more cost risk to the buyer.'],
                    ['text' => 'Time and Materials (T&M)', 'why' => 'T&M suits undefined scope and carries buyer cost risk.'],
                    ['text' => 'Cost Plus Incentive Fee (CPIF)', 'why' => 'Still cost-reimbursable, so the buyer retains cost risk.'],
                ],
            ],
            [
                'domain' => 2, 'knowledge' => 32, 'difficulty' => 'Difficult', 'process_group' => 'Monitoring & Controlling',
                'methodology' => 'Agile', 'cognitive_level' => 'Analyze',
                'pmbok_ref' => 'Uncertainty Performance Domain', 'agile_ref' => 'Impediment Board',
                'exam_tip' => 'Make impediments visible and address the highest-impact one first.',
                'exam_trap' => 'Do not simply work overtime to absorb recurring impediments.',
                'title' => 'An Agile team’s velocity keeps dropping due to recurring environment outages. What should the team do to address the root cause?',
                'explanation' => 'Making the impediment visible and prioritizing a fix for the recurring outage addresses the systemic root cause.',
                'options' => [
                    ['text' => 'Raise it as a top impediment and prioritize a permanent fix.', 'correct' => true],
                    ['text' => 'Ask the team to work overtime to catch up.', 'why' => 'Overtime treats the symptom, not the recurring root cause.'],
                    ['text' => 'Lower the velocity target permanently.', 'why' => 'Hiding the impact does not fix the outages.'],
                    ['text' => 'Remove automated tests to speed up delivery.', 'why' => 'Cutting quality practices increases risk and technical debt.'],
                ],
            ],
        ];
    }
}
