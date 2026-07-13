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

	/**
	 * Taxonomy + option lists that populate the Mock Test Builder dropdowns.
	 */
	public function options(Request $request)
	{
		$domains        = Category::where('type', 1)->where('status', 1)->orderBy('name')->get(['id', 'name']);
		$knowledgeAreas = Category::where('type', 2)->where('status', 1)->orderBy('name')->get(['id', 'name']);
		$approaches     = Category::where('type', 3)->where('status', 1)->orderBy('name')->get(['id', 'name']);

		$difficulties = DB::table('questions')
			->whereNotNull('dificulty_level')->where('dificulty_level', '!=', '')
			->distinct()->orderBy('dificulty_level')->pluck('dificulty_level')->values();

		$processGroups = collect(self::PROCESS_GROUPS)->map(function ($p) {
			return ['id' => $p, 'name' => $p];
		});

		return response()->json([
			'success' => true,
			'data'    => [
				'question_counts' => [180, 120, 90, 60, 30],
				'domains'         => $domains,
				'knowledge_areas' => $knowledgeAreas,
				'approaches'      => $approaches,
				'process_groups'  => $processGroups,
				'difficulties'    => $difficulties,
				'question_types'  => [
					['id' => 1, 'name' => 'Single Choice'],
					['id' => 2, 'name' => 'Multiple Response'],
				],
			],
		]);
	}
}
