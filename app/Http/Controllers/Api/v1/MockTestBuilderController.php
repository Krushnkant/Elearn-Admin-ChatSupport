<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use DB;

class MockTestBuilderController extends Controller
{
	/** Standard PMP process groups (fixed taxonomy). */
	const PROCESS_GROUPS = ['Initiating', 'Planning', 'Executing', 'Monitoring & Controlling', 'Closing'];

	/** Minutes per question, matching the real exam's 180 questions / 230 mins. */
	const MINS_PER_QUESTION = 230 / 180;

	/** "Full Length" means a real exam-length paper, not the whole question bank. */
	const FULL_LENGTH = 180;

	/** A built test needs at least this many questions to be worth sitting. */
	const MIN_QUESTIONS = 5;

	/**
	 * Taxonomy + option lists that populate the Mock Test Builder dropdowns.
	 */
	public function options(Request $request)
	{
		$domains        = Category::where('type', 1)->where('status', 1)->orderBy('name')->get(['id', 'name']);
		$knowledgeAreas = Category::where('type', 2)->where('status', 1)->orderBy('name')->get(['id', 'name']);

		// The column-based taxonomies live as free text on `questions`. Their
		// dropdown values come straight from the DISTINCT column, so every offered
		// option is guaranteed to match at least one question (no empty filters).
		return response()->json([
			'success' => true,
			'data'    => [
				'question_counts' => [180, 120, 90, 60, 30],
				'domains'         => $domains,
				'knowledge_areas' => $knowledgeAreas,
				'process_groups'  => $this->distinctColumn('process_group'),
				'methodologies'   => $this->distinctColumn('methodology'),
				'difficulties'    => $this->distinctColumn('dificulty_level'),
				'cognitive_levels' => $this->distinctColumn('cognitive_level'),
				// "Question Type" here is the PMP question-style taxonomy
				// (Situational, Interpretation, Conceptual, Decision-Making, …)
				// from the question bank's `question_style` column — not the
				// single/multiple-response answer format.
				'question_types'  => $this->distinctColumn('question_style'),
			],
		]);
	}

	/** Distinct non-empty values of a free-text `questions` column as {id,name}. */
	private function distinctColumn($column)
	{
		return DB::table('questions')
			->where('status', 1)
			->whereNotNull($column)->where($column, '!=', '')
			->distinct()->orderBy($column)->pluck($column)
			->map(function ($v) { return ['id' => $v, 'name' => $v]; })
			->values();
	}

	/** Normalise a request filter into a clean list of non-empty values. */
	private function asList($val)
	{
		if (is_array($val)) {
			$val = array_map(function ($v) { return is_string($v) ? trim($v) : $v; }, $val);
			return array_values(array_filter($val, function ($v) { return $v !== '' && $v !== null; }));
		}
		return ($val === null || $val === '') ? [] : [$val];
	}

	/** Pull all six taxonomy filters off the request as normalised lists. */
	private function filtersFrom(Request $request)
	{
		return [
			'domain'          => $this->asList($request->input('domain')),
			'knowledge_area'  => $this->asList($request->input('knowledge_area')),
			'process_group'   => $this->asList($request->input('process_group')),
			'methodology'     => $this->asList($request->input('methodology')),
			'question_type'   => $this->asList($request->input('question_type')),
			'difficulty'      => $this->asList($request->input('difficulty')),
			'cognitive_level' => $this->asList($request->input('cognitive_level')),
		];
	}

	/**
	 * Questions matching the builder's filters, newest-first de-duplicated.
	 * Questions fan out into one row per domain/knowledge/approach, so the same
	 * question can appear several times — de-dup on the title, as the exam
	 * runner does.
	 *
	 * @return array list of question rows, capped at $limit when $limit > 0
	 */
	private function pickQuestions(array $filters, $limit)
	{
		$query = DB::table('questions')->where('status', 1);

		// Domain: a question links to its domain either via the sub_category_id
		// column or the category_questions pivot — match on both. (The old code
		// filtered the pivot on a non-existent `sub_category_id` column, so the
		// domain filter never worked; this fixes it.)
		if (!empty($filters['domain'])) {
			$domains = $filters['domain'];
			$query->where(function ($q) use ($domains) {
				$q->whereIn('sub_category_id', $domains)
				  ->orWhereIn('id', function ($sub) use ($domains) {
					  $sub->select('question_id')->from('category_questions')
						  ->whereIn('category_id', $domains);
				  });
			});
		}
		// Knowledge Area (Category type 2) attaches through the category_questions
		// pivot's sub_category_id — the same place the exam runner reads it from.
		// (questions.category_id holds the DOMAIN id, not the knowledge area, so
		// the old category_id filter matched zero and any KA pick built an empty
		// test.)
		if (!empty($filters['knowledge_area'])) {
			$kas = $filters['knowledge_area'];
			$query->whereIn('id', function ($sub) use ($kas) {
				$sub->select('question_id')->from('category_questions')
					->whereIn('sub_category_id', $kas);
			});
		}
		// The remaining taxonomies are free-text columns on `questions`.
		if (!empty($filters['process_group'])) {
			$query->whereIn('process_group', $filters['process_group']);
		}
		if (!empty($filters['methodology'])) {
			$query->whereIn('methodology', $filters['methodology']);
		}
		if (!empty($filters['question_type'])) {
			$query->whereIn('question_style', $filters['question_type']);
		}
		if (!empty($filters['difficulty'])) {
			$query->whereIn('dificulty_level', $filters['difficulty']);
		}
		if (!empty($filters['cognitive_level'])) {
			$query->whereIn('cognitive_level', $filters['cognitive_level']);
		}

		$rows = $query->inRandomOrder()->get(['id', 'title', 'course_id']);

		$seen   = [];
		$picked = [];
		foreach ($rows as $r) {
			$key = md5(trim((string) $r->title));
			if (isset($seen[$key])) {
				continue;
			}
			$seen[$key] = true;
			$picked[]   = $r;
			if ($limit > 0 && count($picked) >= $limit) {
				break;
			}
		}

		return $picked;
	}

	/** How many questions a given filter combination would yield. */
	public function count(Request $request)
	{
		$n = count($this->pickQuestions($this->filtersFrom($request), 0));

		return response()->json(['success' => true, 'data' => ['available' => $n]]);
	}

	/**
	 * Build a custom mock test from the selected filters.
	 *
	 * The picked questions are persisted as a user-owned assessment, so the
	 * exam runner, progress/resume, submit and score report all work against it
	 * unchanged — they only ever need an assessment id.
	 */
	public function build(Request $request)
	{
		$user = $request->user();

		$num     = (int) $request->input('num', 0);
		$num     = $num > 0 ? $num : self::FULL_LENGTH;  // blank = "Full Length"
		$filters = $this->filtersFrom($request);

		$picked = $this->pickQuestions($filters, $num);
		$count  = count($picked);

		if ($count < self::MIN_QUESTIONS) {
			return response()->json([
				'success' => false,
				'message' => $count === 0
					? 'No questions match those filters. Try widening your selection.'
					: 'Only ' . $count . ' question(s) match those filters — at least ' . self::MIN_QUESTIONS . ' are needed. Try widening your selection.',
			], 422);
		}

		// Readable title: "Custom Mock Test — People · Complex (90 Q)". Domain and
		// Knowledge Area are category ids (resolved to names); the rest are the
		// picked values themselves.
		$catIds = array_merge($filters['domain'], $filters['knowledge_area']);
		$bits   = $catIds
			? Category::whereIn('id', $catIds)->pluck('name')->all()
			: [];
		$bits = array_merge(
			$bits,
			$filters['process_group'],
			$filters['methodology'],
			$filters['question_type'],
			$filters['difficulty'],
			$filters['cognitive_level']
		);
		$label = $bits ? implode(' · ', array_slice($bits, 0, 4)) . (count($bits) > 4 ? ' …' : '') : '';
		$title = 'Custom Mock Test' . ($label ? ' — ' . $label : '') . ' (' . $count . ' Q)';

		$now = date('Y-m-d H:i:s');
		$assessmentId = DB::table('assessments')->insertGetId([
			'course_id'           => (int) ($picked[0]->course_id ?: 1),
			'title'               => $title,
			'status'              => 1,
			'number_of_questions' => $count,
			'duration_mins'       => max(1, (int) ceil($count * self::MINS_PER_QUESTION)),
			'skill_id'            => 1,
			'mock_exam'           => 'custom',
			'is_custom'           => 1,
			'user_id'             => $user->id,
			'created_at'          => $now,
			'updated_at'          => $now,
		]);

		$link = [];
		foreach ($picked as $i => $p) {
			$link[] = ['assessment_id' => $assessmentId, 'question_id' => $p->id, 'position' => $i];
		}
		foreach (array_chunk($link, 500) as $chunk) {
			DB::table('assessment_questions')->insert($chunk);
		}

		return response()->json([
			'success' => true,
			'message' => 'Mock test generated.',
			'data'    => [
				'assessment_id'       => $assessmentId,
				'title'               => $title,
				'number_of_questions' => $count,
				'duration_mins'       => max(1, (int) ceil($count * self::MINS_PER_QUESTION)),
			],
		]);
	}
}
