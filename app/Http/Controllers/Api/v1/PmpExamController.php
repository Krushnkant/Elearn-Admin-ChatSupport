<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Question;
use Carbon\Carbon;
use DB;

class PmpExamController extends Controller
{
	/**
	 * Questions for an assessment, ready for the timed exam runner.
	 * De-duplicates the fanned-out rows (same question exists once per
	 * domain/knowledge/approach) and never exposes which option is correct.
	 */
	public function questions(Request $request, $assessmentId)
	{
		$a = Assessment::where('status', 1)->findOrFail($assessmentId);

		$limit = max(1, (int) $a->number_of_questions);

		$cols = ['id', 'title', 'question_type', 'explanation', 'dificulty_level',
		         'process_group', 'methodology', 'cognitive_level', 'pmbok_ref',
		         'agile_ref', 'exam_tip', 'exam_trap', 'eco_task',
		         'topic_category', 'tested_skill', 'question_style'];

		if (!empty($a->is_custom)) {
			// Builder-generated test: its question set lives in the link table,
			// since questions.assessment_id belongs to the curated test they
			// were drawn from. `position` preserves the order they were picked.
			$rows = Question::join('assessment_questions as aq', 'aq.question_id', '=', 'questions.id')
				->where('aq.assessment_id', $assessmentId)
				->where('questions.status', 1)
				->with(['questionOptions:id,question_id,options,is_correct,why_wrong,why_correct'])
				->orderBy('aq.position', 'asc')
				->get(array_map(function ($c) { return 'questions.' . $c; }, $cols));
		} else {
			$rows = Question::where('assessment_id', $assessmentId)
				->where('status', 1)
				->with(['questionOptions:id,question_id,options,is_correct,why_wrong,why_correct'])
				->orderBy('id', 'asc')
				->get($cols);
		}

		// De-dup the fanned-out rows (same question once per domain/knowledge/approach).
		$seen   = [];
		$picked = [];
		foreach ($rows as $q) {
			$key = md5(trim((string) $q->title));
			if (isset($seen[$key])) {
				continue;
			}
			$seen[$key] = true;
			$picked[]   = $q;
			if (count($picked) >= $limit) {
				break;
			}
		}

		// Domain (category type 1) + Knowledge Area (type 2) per question, batched.
		$ids       = array_map(function ($q) { return $q->id; }, $picked);
		$domainBy  = [];
		$knowBy    = [];
		if (!empty($ids)) {
			$cats = DB::table('category_questions as cq')
				->join('categories as c', 'c.id', '=', 'cq.sub_category_id')
				->whereIn('cq.question_id', $ids)
				->get(['cq.question_id', 'c.name', 'c.type']);
			foreach ($cats as $c) {
				if ((int) $c->type === 1 && !isset($domainBy[$c->question_id])) { $domainBy[$c->question_id] = $c->name; }
				if ((int) $c->type === 2 && !isset($knowBy[$c->question_id]))   { $knowBy[$c->question_id]   = $c->name; }
			}
		}

		$questions = [];
		foreach ($picked as $q) {
			// Rich PMP metadata shown in the answer-feedback panel (empties dropped).
			$meta = array_values(array_filter([
				isset($domainBy[$q->id]) ? ['ic' => 'bi-people',         'lbl' => 'PMP Domain',                    'val' => $domainBy[$q->id]] : null,
				$q->eco_task             ? ['ic' => 'bi-list-check',     'lbl' => 'ECO Task',                      'val' => $q->eco_task]        : null,
				$q->topic_category       ? ['ic' => 'bi-tags',           'lbl' => 'Topic Category',                'val' => $q->topic_category]  : null,
				$q->tested_skill         ? ['ic' => 'bi-journal-bookmark', 'lbl' => 'Tested Skill / Topic',        'val' => $q->tested_skill]    : null,
				$q->methodology          ? ['ic' => 'bi-signpost-split', 'lbl' => 'Methodology',                   'val' => $q->methodology]     : null,
				$q->question_style       ? ['ic' => 'bi-patch-question', 'lbl' => 'Question Type',                 'val' => $q->question_style]  : null,
				$q->dificulty_level      ? ['ic' => 'bi-bar-chart-steps','lbl' => 'Difficulty',                    'val' => $q->dificulty_level, 'tag' => true] : null,
				$q->cognitive_level      ? ['ic' => 'bi-lightbulb',      'lbl' => 'Cognitive Level',               'val' => $q->cognitive_level] : null,
				isset($knowBy[$q->id])   ? ['ic' => 'bi-diagram-3',      'lbl' => 'PMBOK Topic / Knowledge Area',  'val' => $knowBy[$q->id]]      : null,
				$q->process_group        ? ['ic' => 'bi-kanban',         'lbl' => 'Predictive Process Group / Lifecycle Stage', 'val' => $q->process_group] : null,
				$q->pmbok_ref            ? ['ic' => 'bi-book',           'lbl' => 'PMBOK® Reference',              'val' => $q->pmbok_ref]       : null,
				$q->agile_ref            ? ['ic' => 'bi-signpost-2',     'lbl' => 'Agile Reference',               'val' => $q->agile_ref]       : null,
				$q->exam_tip             ? ['ic' => 'bi-lightbulb-fill', 'lbl' => 'PMI Exam Tip',                  'val' => $q->exam_tip]        : null,
				$q->exam_trap            ? ['ic' => 'bi-exclamation-triangle','lbl' => 'Exam Trap',                'val' => $q->exam_trap]       : null,
			]));

			$questions[] = [
				'id'            => $q->id,
				'title'         => $q->title,
				'question_type' => (int) $q->question_type, // 1 single, 2 multiple
				'explanation'   => $q->explanation,
				'meta'          => $meta,
				'options'       => $q->questionOptions->map(function ($o) {
					return [
						'id'          => $o->id,
						'text'        => $o->options,
						'is_correct'  => (int) $o->is_correct === 1,
						'why_wrong'   => $o->why_wrong,
						'why_correct' => $o->why_correct,
					];
				})->values(),
			];
		}

		return response()->json([
			'success' => true,
			'data'    => [
				'assessment' => [
					'id'                  => $a->id,
					'title'               => $a->title,
					'number_of_questions' => count($questions),
					'duration_mins'       => (int) $a->duration_mins,
				],
				'questions' => $questions,
			],
		]);
	}

	/**
	 * Saved in-progress state for this user + assessment (so the student can
	 * close the exam and resume from the same question). Null when none.
	 */
	public function getProgress(Request $request, $assessmentId)
	{
		$user = $request->user();
		$row = DB::table('assessment_progress')
			->where('user_id', $user->id)
			->where('assessment_id', $assessmentId)
			->first();

		$data = null;
		if ($row) {
			$data = [
				'answers'        => json_decode($row->answers ?: '{}', true) ?: (object) [],
				'marked'         => json_decode($row->marked ?: '[]', true) ?: [],
				'current_index'  => (int) $row->current_index,
				'time_taken_sec' => (int) $row->time_taken_sec,
			];
		}

		return response()->json(['success' => true, 'data' => $data]);
	}

	/**
	 * Upsert the in-progress exam state. Called as the student answers /
	 * navigates and on leaving the page.
	 *
	 * Body: answers (object), marked (array), current_index, time_taken_sec
	 */
	public function saveProgress(Request $request, $assessmentId)
	{
		$user = $request->user();
		// Validate the assessment exists but keep this cheap — it's called often.
		Assessment::findOrFail($assessmentId);

		$answers = $request->input('answers', []);
		$marked  = $request->input('marked', []);

		DB::table('assessment_progress')->updateOrInsert(
			['user_id' => $user->id, 'assessment_id' => (int) $assessmentId],
			[
				'answers'        => json_encode($answers ?: (object) []),
				'marked'         => json_encode(array_values((array) $marked)),
				'current_index'  => (int) $request->input('current_index', 0),
				'time_taken_sec' => (int) $request->input('time_taken_sec', 0),
				'updated_at'     => date('Y-m-d H:i:s'),
				'created_at'     => date('Y-m-d H:i:s'),
			]
		);

		return response()->json(['success' => true]);
	}

	/**
	 * Submit an attempt. Scores server-side (never trusts the client for
	 * correctness), writes a test_results row + answers rows compatible with
	 * the score report, and returns the new result id.
	 *
	 * Body: answers = [ {question_id, answer_ids: []}, ... ]
	 */
	public function submit(Request $request, $assessmentId)
	{
		$user = $request->user();
		$a = Assessment::findOrFail($assessmentId);

		$answers = $request->input('answers', []);
		if (is_string($answers)) {
			$answers = json_decode($answers, true) ?: [];
		}

		$resultId = DB::table('test_results')->insertGetId([
			'course_id'      => $a->course_id,
			'assessment_id'  => $a->id,
			'user_id'        => $user->id,
			'time_taken_sec' => (int) $request->input('time_taken_sec', 0),
			'created_at'     => date('Y-m-d H:i:s'),
			'updated_at'     => date('Y-m-d H:i:s'),
		]);

		$rows = [];
		foreach ($answers as $ans) {
			$qid    = isset($ans['question_id']) ? (int) $ans['question_id'] : 0;
			$chosen = isset($ans['answer_ids']) ? array_map('intval', (array) $ans['answer_ids']) : [];
			if (!$qid) {
				continue;
			}

			$correct = DB::table('question_options')
				->where('question_id', $qid)->where('is_correct', 1)
				->pluck('id')->map(function ($v) { return (int) $v; })->toArray();

			if (empty($chosen)) {
				$isCorrect = 2; // skipped / unattempted
			} else {
				$isCorrect = (empty(array_diff($correct, $chosen)) && empty(array_diff($chosen, $correct))) ? 1 : 0;
			}

			$rows[] = [
				'mock_test_id'  => $resultId,
				'question_id'   => $qid,
				'answer_id'     => implode(',', $chosen),
				'is_correctans' => $isCorrect,
				'created_at'    => date('Y-m-d H:i:s'),
				'updated_at'    => date('Y-m-d H:i:s'),
			];
		}

		if (!empty($rows)) {
			DB::table('answers')->insert($rows);
		}

		// Attempt is finished — drop the saved in-progress state.
		DB::table('assessment_progress')
			->where('user_id', $user->id)
			->where('assessment_id', $a->id)
			->delete();

		// Notify the learner their attempt has been scored — same
		// attempted/correct rule as the score report (skipped rows, marked
		// 2 above, don't count either way).
		$graded  = array_filter($rows, function ($r) { return $r['is_correctans'] !== 2; });
		$correct = array_filter($graded, function ($r) { return $r['is_correctans'] === 1; });
		$pct     = count($graded) > 0 ? round(count($correct) / count($graded) * 100) : 0;
		\App\Models\Notification::notify(
			$user->id,
			'Mock test scored: ' . $a->title,
			"You scored {$pct}% (" . count($correct) . '/' . count($graded) . ' correct). Tap to view your full report.',
			'mock_test'
		);

		return response()->json([
			'success' => true,
			'data'    => ['result_id' => $resultId],
		]);
	}

	/**
	 * Full analytics report for a completed attempt.
	 */
	public function result(Request $request, $resultId)
	{
		$tr = DB::table('test_results')->where('id', $resultId)->first();
		if (!$tr) {
			return response()->json(['success' => false, 'message' => 'Result not found'], 404);
		}

		$assessment = DB::table('assessments')->where('id', $tr->assessment_id)->first();
		$user       = DB::table('users')->where('id', $tr->user_id)->first();

		// All answers joined to their question (for difficulty + category lookups).
		$answers = DB::table('answers as a')
			->join('questions as q', 'q.id', '=', 'a.question_id')
			->where('a.mock_test_id', $resultId)
			->get(['a.question_id', 'a.is_correctans', 'q.dificulty_level']);

		$total   = $answers->count();
		$correct = $answers->where('is_correctans', 1)->count();
		$pct     = $total > 0 ? round($correct / $total * 100) : 0;

		// Category-wise (domain = type 1, knowledge/topics = type 2) via the
		// category_questions pivot, joined to categories by the actual category id.
		$byType = function ($type) use ($resultId) {
			return DB::table('answers as a')
				->join('category_questions as cq', 'cq.question_id', '=', 'a.question_id')
				->join('categories as c', 'c.id', '=', 'cq.sub_category_id')
				->where('a.mock_test_id', $resultId)
				->where('c.type', $type)
				->groupBy('c.id', 'c.name')
				->get([
					'c.name',
					DB::raw('COUNT(DISTINCT a.question_id) as q'),
					DB::raw('SUM(CASE WHEN a.is_correctans = 1 THEN 1 ELSE 0 END) as c'),
				]);
		};

		$mapCat = function ($rows) {
			return $rows->map(function ($r) {
				$q = (int) $r->q; $c = (int) $r->c;
				return ['name' => $r->name, 'q' => $q, 'c' => $c, 'a' => $q > 0 ? round($c / $q * 100) : 0];
			})->values();
		};

		$domains = $mapCat($byType(1));
		$topics  = $mapCat($byType(2));

		// Difficulty breakdown from the question's dificulty_level.
		$difficulty = $answers->groupBy(function ($r) {
			return trim($r->dificulty_level) ?: 'Unspecified';
		})->map(function ($grp, $name) {
			$q = $grp->count();
			$c = $grp->where('is_correctans', 1)->count();
			return ['name' => $name, 'q' => $q, 'c' => $c, 'a' => $q > 0 ? round($c / $q * 100) : 0];
		})->values();

		// Strengths / improvements from category accuracy.
		$ranked      = collect($domains)->merge($topics)->filter(function ($x) { return $x['q'] >= 1; });
		$strengths   = $ranked->sortByDesc('a')->take(5)->pluck('name')->values();
		$improve     = $ranked->sortBy('a')->take(5)->pluck('name')->values();

		$recos = [];
        $recos[] = 'Review the questions you answered incorrectly and read each explanation.';
        if ($improve->count()) { $recos[] = 'Focus your study on: ' . $improve->take(3)->implode(', ') . '.'; }
        $recos[] = 'Attempt more full-length mock tests to build speed and accuracy.';
        if ($pct < 70) { $recos[] = 'Aim for 70%+ before booking your exam — revisit weak domains.'; }

		$timeTaken = (int) ($tr->time_taken_sec ?? 0);
		$avgPerQ   = $total > 0 && $timeTaken > 0 ? round($timeTaken / $total) : 0;

		return response()->json([
			'success' => true,
			'data'    => [
				'candidate'   => $user->name ?? 'Candidate',
				'test_name'   => $assessment->title ?? 'Mock Test',
				'test_date'   => date('d M Y', strtotime($tr->created_at)),
				'time_taken'  => $this->hms($timeTaken),
				'avg_per_q'   => $avgPerQ ? ($avgPerQ . ' sec') : '—',
				'overall_pct' => $pct,
				'readiness'   => min(100, $pct),
				'total_q'     => $total,
				'total_c'     => $correct,
				'domains'     => $domains,
				'topics'      => $topics,
				'difficulty'  => $difficulty,
				'strengths'   => $strengths,
				'improve'     => $improve,
				'recos'       => $recos,
			],
		]);
	}

	private function hms($sec)
	{
		$sec = max(0, (int) $sec);
		return sprintf('%02d:%02d:%02d', floor($sec / 3600), floor(($sec % 3600) / 60), $sec % 60);
	}

	/**
	 * Per-question review of a completed attempt: correct answer, the user's
	 * choice, per-option why-wrong, explanation and the rich PMP metadata.
	 */
	public function review(Request $request, $resultId)
	{
		$tr = DB::table('test_results')->where('id', $resultId)->first();
		if (!$tr) {
			return response()->json(['success' => false, 'message' => 'Result not found'], 404);
		}

		$answers = DB::table('answers')->where('mock_test_id', $resultId)->get(['question_id', 'answer_id', 'is_correctans']);

		$out = [];
		foreach ($answers as $ans) {
			$q = Question::with(['questionOptions', 'category:id,name', 'knowledgeArea:id,name'])->find($ans->question_id);
			if (!$q) { continue; }

			$chosen = array_filter(array_map('intval', explode(',', (string) $ans->answer_id)));

			$options = $q->questionOptions->map(function ($o) use ($chosen) {
				return [
					'text'        => $o->options,
					'is_correct'  => (int) $o->is_correct === 1,
					'why_wrong'   => $o->why_wrong,
					'why_correct' => $o->why_correct,
					'chosen'      => in_array((int) $o->id, $chosen, true),
				];
			})->values();

			$meta = array_values(array_filter([
				$q->category ? ['lbl' => 'PMP Domain',                      'val' => $q->category->name] : null,
				$q->eco_task ? ['lbl' => 'ECO Task',                        'val' => $q->eco_task] : null,
				$q->topic_category ? ['lbl' => 'Topic Category',           'val' => $q->topic_category] : null,
				$q->tested_skill ? ['lbl' => 'Tested Skill / Topic',        'val' => $q->tested_skill] : null,
				$q->methodology ? ['lbl' => 'Methodology',                  'val' => $q->methodology] : null,
				$q->question_style ? ['lbl' => 'Question Type',             'val' => $q->question_style] : null,
				$q->dificulty_level ? ['lbl' => 'Difficulty',               'val' => $q->dificulty_level] : null,
				$q->cognitive_level ? ['lbl' => 'Cognitive Level',          'val' => $q->cognitive_level] : null,
				$q->knowledgeArea ? ['lbl' => 'PMBOK Topic / Knowledge Area','val' => $q->knowledgeArea->name] : null,
				$q->process_group ? ['lbl' => 'Predictive Process Group / Lifecycle Stage', 'val' => $q->process_group] : null,
				$q->pmbok_ref ? ['lbl' => 'PMBOK Reference',                'val' => $q->pmbok_ref] : null,
				$q->agile_ref ? ['lbl' => 'Agile Reference',                'val' => $q->agile_ref] : null,
				$q->exam_tip ? ['lbl' => 'PMI Exam Tip',                    'val' => $q->exam_tip] : null,
				$q->exam_trap ? ['lbl' => 'Exam Trap',                      'val' => $q->exam_trap] : null,
			]));

			$out[] = [
				'title'       => $q->title,
				'explanation' => $q->explanation,
				'is_correct'  => (int) $ans->is_correctans === 1,
				'attempted'   => !empty($chosen),
				'options'     => $options,
				'meta'        => $meta,
			];
		}

		return response()->json(['success' => true, 'data' => ['questions' => $out]]);
	}

	/**
	 * "My Progress" summary for the logged-in student: tests taken, average
	 * per-attempt score, current day streak, and a recent-attempts list —
	 * everything the app's My Progress screen needs in one call.
	 */
	public function myProgress(Request $request)
	{
		$userId = $request->user()->id;

		$results = DB::table('test_results as t')
			->join('assessments as a', 'a.id', '=', 't.assessment_id')
			->where('t.user_id', $userId)
			->orderByDesc('t.id')
			->get(['t.id', 't.created_at', 'a.title']);

		$testsTaken = $results->count();

		// Per-attempt score %, graded answers only (same correctness rule
		// used everywhere else: is_correctans NULL = ungraded legacy row).
		$scoresByResult = DB::table('answers')
			->whereIn('mock_test_id', $results->pluck('id'))
			->whereNotNull('is_correctans')
			->select('mock_test_id',
				DB::raw('COUNT(*) as total'),
				DB::raw('SUM(CASE WHEN is_correctans > 0 THEN 1 ELSE 0 END) as correct'))
			->groupBy('mock_test_id')
			->get()
			->keyBy('mock_test_id');

		$attempts = [];
		$scoreSum = 0;
		$scoreCount = 0;
		foreach ($results as $r) {
			$s = $scoresByResult->get($r->id);
			$total = $s ? (int) $s->total : 0;
			$correct = $s ? (int) $s->correct : 0;
			$pct = $total > 0 ? (int) round($correct / $total * 100) : null;
			if ($pct !== null) {
				$scoreSum += $pct;
				$scoreCount++;
			}
			$attempts[] = [
				'id'        => $r->id,
				'title'     => $r->title,
				'date'      => date('d M Y', strtotime($r->created_at)),
				'score_pct' => $pct,
				'correct'   => $correct,
				'total'     => $total,
			];
		}
		$avgScore = $scoreCount > 0 ? (int) round($scoreSum / $scoreCount) : null;

		// Current day streak: consecutive calendar days with at least one
		// attempt, walking back from the most recent active day — as long
		// as that day is today or yesterday (otherwise the streak is over).
		$dates = DB::table('test_results')->where('user_id', $userId)
			->selectRaw('DISTINCT DATE(created_at) as d')
			->pluck('d')
			->map(function ($d) { return Carbon::parse($d)->startOfDay(); })
			->sortByDesc(function ($d) { return $d->timestamp; })
			->values();

		$streak = 0;
		if ($dates->isNotEmpty()) {
			$today = Carbon::today();
			if ($dates->first()->diffInDays($today) <= 1) {
				$cursor = $dates->first()->copy();
				foreach ($dates as $d) {
					if ($d->equalTo($cursor)) {
						$streak++;
						$cursor->subDay();
					} elseif ($d->lt($cursor)) {
						break;
					}
				}
			}
		}

		return response()->json([
			'success' => true,
			'data'    => [
				'tests_taken' => $testsTaken,
				'avg_score'   => $avgScore,
				'day_streak'  => $streak,
				'attempts'    => array_slice($attempts, 0, 20),
			],
		]);
	}
}
