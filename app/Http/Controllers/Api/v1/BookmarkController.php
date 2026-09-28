<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Question;
use DB;

class BookmarkController extends Controller
{
	/**
	 * Bookmarked question ids for the logged-in user. The exam runner uses this
	 * to show the bookmark state as the learner moves between questions.
	 */
	public function ids(Request $request)
	{
		$ids = DB::table('bookmarks')
			->where('user_id', $request->user()->id)
			->pluck('question_id');

		return response()->json(['success' => true, 'data' => $ids]);
	}

	/**
	 * Add or remove a bookmark. Returns the resulting state so the caller never
	 * has to guess which way the toggle went.
	 */
	public function toggle(Request $request)
	{
		$userId     = $request->user()->id;
		$questionId = (int) $request->input('question_id', 0);

		if (!$questionId || !Question::where('id', $questionId)->exists()) {
			return response()->json(['success' => false, 'message' => 'Unknown question.'], 422);
		}

		$existing = DB::table('bookmarks')
			->where('user_id', $userId)
			->where('question_id', $questionId)
			->first();

		if ($existing) {
			DB::table('bookmarks')->where('id', $existing->id)->delete();
			$bookmarked = false;
		} else {
			$now = date('Y-m-d H:i:s');
			DB::table('bookmarks')->insert([
				'user_id'     => $userId,
				'question_id' => $questionId,
				'created_at'  => $now,
				'updated_at'  => $now,
			]);
			$bookmarked = true;
		}

		return response()->json([
			'success' => true,
			'data'    => ['question_id' => $questionId, 'bookmarked' => $bookmarked],
		]);
	}

	/**
	 * The "My Bookmarks" page: each saved question with its options, the answer
	 * key, explanation and PMP metadata, newest first.
	 */
	public function index(Request $request)
	{
		$rows = DB::table('bookmarks as b')
			->join('questions as q', 'q.id', '=', 'b.question_id')
			->leftJoin('assessments as a', 'a.id', '=', 'q.assessment_id')
			->where('b.user_id', $request->user()->id)
			->where('q.status', 1)
			->orderByDesc('b.id')
			->get([
				'q.id', 'q.title', 'q.question_type', 'q.explanation', 'q.dificulty_level',
				'q.process_group', 'q.methodology', 'q.cognitive_level', 'q.pmbok_ref',
				'q.agile_ref', 'q.exam_tip', 'q.exam_trap',
				'a.title as test_name', 'b.created_at as saved_at',
			]);

		$ids = $rows->pluck('id')->all();

		// Options and categories batched, rather than a query per question.
		$optionsBy = [];
		if ($ids) {
			foreach (DB::table('question_options')->whereIn('question_id', $ids)
				->get(['id', 'question_id', 'options', 'is_correct', 'why_wrong']) as $o) {
				$optionsBy[$o->question_id][] = [
					'id'         => $o->id,
					'text'       => $o->options,
					'is_correct' => (int) $o->is_correct === 1,
					'why_wrong'  => $o->why_wrong,
				];
			}
		}

		$domainBy = [];
		$knowBy   = [];
		if ($ids) {
			$cats = DB::table('category_questions as cq')
				->join('categories as c', 'c.id', '=', 'cq.sub_category_id')
				->whereIn('cq.question_id', $ids)
				->get(['cq.question_id', 'c.name', 'c.type']);
			foreach ($cats as $c) {
				if ((int) $c->type === 1 && !isset($domainBy[$c->question_id])) { $domainBy[$c->question_id] = $c->name; }
				if ((int) $c->type === 2 && !isset($knowBy[$c->question_id]))   { $knowBy[$c->question_id]   = $c->name; }
			}
		}

		$data = [];
		foreach ($rows as $q) {
			$meta = array_values(array_filter([
				isset($domainBy[$q->id]) ? ['ic' => 'bi-people',    'lbl' => 'Domain',          'val' => $domainBy[$q->id]] : null,
				isset($knowBy[$q->id])   ? ['ic' => 'bi-diagram-3', 'lbl' => 'Knowledge Area',  'val' => $knowBy[$q->id]]   : null,
				$q->process_group   ? ['ic' => 'bi-kanban',          'lbl' => 'Process Group',   'val' => $q->process_group]   : null,
				$q->methodology     ? ['ic' => 'bi-signpost-split',  'lbl' => 'Methodology',     'val' => $q->methodology]     : null,
				$q->dificulty_level ? ['ic' => 'bi-bar-chart-steps', 'lbl' => 'Difficulty',      'val' => $q->dificulty_level] : null,
				$q->cognitive_level ? ['ic' => 'bi-lightbulb',       'lbl' => 'Cognitive Level', 'val' => $q->cognitive_level] : null,
				$q->pmbok_ref       ? ['ic' => 'bi-book',            'lbl' => 'PMBOK® Reference','val' => $q->pmbok_ref]       : null,
				$q->exam_tip        ? ['ic' => 'bi-lightbulb-fill',  'lbl' => 'PMI Exam Tip',    'val' => $q->exam_tip]        : null,
				$q->exam_trap       ? ['ic' => 'bi-exclamation-triangle', 'lbl' => 'Exam Trap',  'val' => $q->exam_trap]       : null,
			]));

			$data[] = [
				'id'            => $q->id,
				'title'         => $q->title,
				'question_type' => (int) $q->question_type,
				'explanation'   => $q->explanation,
				'test_name'     => $q->test_name,
				'saved_at'      => $q->saved_at ? date('d M Y', strtotime($q->saved_at)) : null,
				'meta'          => $meta,
				'options'       => $optionsBy[$q->id] ?? [],
			];
		}

		return response()->json([
			'success' => true,
			'message' => 'Data successfully found.',
			'data'    => $data,
		]);
	}
}
