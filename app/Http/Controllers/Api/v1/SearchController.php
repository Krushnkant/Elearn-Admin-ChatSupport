<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\LiveVideo;
use App\Models\StudyMaterial;
use DB;

class SearchController extends Controller
{
	/**
	 * Real content search across the PMP Learning Hub — mock tests, study
	 * material books and live-video sessions — for the header search box on
	 * both the app and the website (same API, same results).
	 */
	public function index(Request $request)
	{
		$q = trim((string) $request->query('q', ''));
		$userId = optional($request->user())->id;

		if ($q === '') {
			return response()->json([
				'success' => true,
				'data'    => ['mock_tests' => [], 'study_materials' => [], 'live_videos' => []],
			]);
		}

		$like = '%' . $q . '%';

		// Curated tests for everyone, plus this user's own custom ones — same
		// scoping as the Mock Test list, so search never surfaces someone
		// else's private builder-generated test.
		$mockTests = Assessment::where('status', 1)
			->where('title', 'like', $like)
			->where(function ($query) use ($userId) {
				$query->where(function ($q2) {
					$q2->where('is_custom', 0)->orWhereNull('is_custom');
				});
				if ($userId) {
					$query->orWhere(function ($q2) use ($userId) {
						$q2->where('is_custom', 1)->where('user_id', $userId);
					});
				}
			})
			->orderBy('title')
			->limit(8)
			->get(['id', 'title', 'number_of_questions', 'duration_mins'])
			->map(function ($a) {
				return [
					'id'                  => $a->id,
					'title'               => $a->title,
					'number_of_questions' => (int) $a->number_of_questions,
					'duration_mins'       => (int) $a->duration_mins,
				];
			});

		$studyMaterials = StudyMaterial::where('status', 1)
			->where(function ($query) use ($like) {
				$query->where('title', 'like', $like)
					->orWhere('description', 'like', $like);
			})
			->orderBy('sort_order')
			->limit(8)
			->get(['id', 'title', 'description', 'icon', 'color'])
			->map(function ($b) {
				return [
					'id'          => $b->id,
					'title'       => $b->title,
					'description' => $b->description,
					'icon'        => $b->icon,
					'color'       => $b->color,
				];
			});

		$liveVideos = LiveVideo::where('status', 1)
			->where(function ($query) use ($like) {
				$query->where('title', 'like', $like)
					->orWhere('description', 'like', $like);
			})
			->orderBy('day_no')
			->limit(8)
			->get(['id', 'day_no', 'title', 'description'])
			->map(function ($lv) {
				return [
					'id'     => $lv->id,
					'day_no' => (int) $lv->day_no,
					'title'  => $lv->title,
				];
			});

		return response()->json([
			'success' => true,
			'data'    => [
				'mock_tests'      => $mockTests,
				'study_materials' => $studyMaterials,
				'live_videos'     => $liveVideos,
			],
		]);
	}
}
