<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Illuminate\Support\Facades\Schema;
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

		$assessments = Assessment::where('status', 1)
			->orderBy('id', 'asc')
			->get(['id', 'title', 'number_of_questions', 'duration_mins']);

		$hasRemaining = Schema::hasTable('test_remaining');

		$data = $assessments->map(function ($a) use ($userId, $hasRemaining) {
			$status   = 'todo';
			$resultId = null;

			if ($userId) {
				// In-progress (saved but not submitted) takes priority.
				$inProgress = $hasRemaining
					? DB::table('test_remaining')
						->where('user_id', $userId)
						->where('assessment_id', $a->id)
						->exists()
					: false;

				$lastResult = DB::table('test_results')
					->where('user_id', $userId)
					->where('assessment_id', $a->id)
					->orderByDesc('id')
					->first(['id']);

				if ($inProgress) {
					$status = 'progress';
				} elseif ($lastResult) {
					$status   = 'done';
					$resultId = $lastResult->id;
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
