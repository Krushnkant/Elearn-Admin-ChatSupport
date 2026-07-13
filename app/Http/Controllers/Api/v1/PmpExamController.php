<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Question;
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

		$rows = Question::where('assessment_id', $assessmentId)
			->where('status', 1)
			->with(['questionOptions:id,question_id,options'])
			->orderBy('id', 'asc')
			->get(['id', 'title', 'question_type']);

		$seen = [];
		$questions = [];
		foreach ($rows as $q) {
			$key = md5(trim((string) $q->title));
			if (isset($seen[$key])) {
				continue; // collapse the domain/knowledge/approach duplicates
			}
			$seen[$key] = true;

			$questions[] = [
				'id'            => $q->id,
				'title'         => $q->title,
				'question_type' => (int) $q->question_type, // 1 single, 2 multiple
				'options'       => $q->questionOptions->map(function ($o) {
					return ['id' => $o->id, 'text' => $o->options];
				})->values(),
			];

			if (count($questions) >= $limit) {
				break;
			}
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
			$q = Question::with(['questionOptions', 'category:id,name'])->find($ans->question_id);
			if (!$q) { continue; }

			$chosen = array_filter(array_map('intval', explode(',', (string) $ans->answer_id)));

			$options = $q->questionOptions->map(function ($o) use ($chosen) {
				return [
					'text'       => $o->options,
					'is_correct' => (int) $o->is_correct === 1,
					'why_wrong'  => $o->why_wrong,
					'chosen'     => in_array((int) $o->id, $chosen, true),
				];
			})->values();

			$meta = array_values(array_filter([
				$q->category ? ['lbl' => 'Category',            'val' => $q->category->name] : null,
				$q->dificulty_level ? ['lbl' => 'Difficulty',  'val' => $q->dificulty_level] : null,
				$q->process_group ? ['lbl' => 'Process Group', 'val' => $q->process_group] : null,
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
}
