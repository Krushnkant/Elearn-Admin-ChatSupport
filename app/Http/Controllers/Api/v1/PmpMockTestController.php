<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use DB;

class PmpMockTestController extends Controller
{
	/**
	 * Mock-test list for the new PMP mockTest page, with per-user attempt
	 * status (done / progress / todo) and the ids the UI links need.
	 */
	public function index(Request $request)
	{
		$userId = optional($request->user())->id;

		// Curated tests for everyone, plus this user's own builder-generated
		// tests (a custom test belongs to exactly one user, so it must never
		// leak into another user's list).
		$assessments = Assessment::where('status', 1)
			->where(function ($q) use ($userId) {
				$q->where(function ($q2) {
					$q2->where('is_custom', 0)->orWhereNull('is_custom');
				});
				if ($userId) {
					$q->orWhere(function ($q2) use ($userId) {
						$q2->where('is_custom', 1)->where('user_id', $userId);
					});
				}
			})
			->orderBy('id', 'asc')
			->get(['id', 'title', 'number_of_questions', 'duration_mins']);

		$data = $assessments->map(function ($a) use ($userId) {
			$status   = 'todo';
			$resultId = null;

			if ($userId) {
				// An assessment_progress row means an attempt is still in flight:
				// submit() deletes it once the test is finalised. This takes
				// priority over any past result, so an unfinished attempt reads
				// as 'progress' even if the user completed the test previously.
				$inProgress = DB::table('assessment_progress')
					->where('user_id', $userId)
					->where('assessment_id', $a->id)
					->exists();

				$lastResult = DB::table('test_results')
					->where('user_id', $userId)
					->where('assessment_id', $a->id)
					->orderByDesc('id')
					->first(['id']);

				// The newest finished attempt's report stays linked either way,
				// so a resumable test can still offer "View Report" for it.
				$resultId = $lastResult ? $lastResult->id : null;

				if ($inProgress) {
					$status = 'progress';
				} elseif ($lastResult) {
					$status = 'done';
				}
			}

			return [
				'id'                  => $a->id,
				'title'               => $a->title,
				'number_of_questions' => (int) $a->number_of_questions,
				'duration_mins'       => (int) $a->duration_mins,
				'status'              => $status,
				'result_id'           => $resultId,
			];
		});

		return response()->json([
			'success' => true,
			'message' => "Data successfully found.",
			'data'    => $data,
		]);
	}
}
