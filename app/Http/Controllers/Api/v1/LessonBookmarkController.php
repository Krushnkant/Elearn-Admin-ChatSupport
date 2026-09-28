<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StudyMaterialLesson;
use DB;

class LessonBookmarkController extends Controller
{
	/**
	 * Bookmarked lesson ids for the logged-in user. courseReader uses this to
	 * show the bookmark state as the learner moves between lessons.
	 */
	public function ids(Request $request)
	{
		$ids = DB::table('lesson_bookmarks')
			->where('user_id', $request->user()->id)
			->pluck('study_material_lesson_id');

		return response()->json(['success' => true, 'data' => $ids]);
	}

	/**
	 * Add or remove a lesson bookmark. Returns the resulting state so the
	 * caller never has to guess which way the toggle went.
	 */
	public function toggle(Request $request)
	{
		$userId   = $request->user()->id;
		$lessonId = (int) $request->input('lesson_id', 0);

		if (!$lessonId || !StudyMaterialLesson::where('id', $lessonId)->exists()) {
			return response()->json(['success' => false, 'message' => 'Unknown lesson.'], 422);
		}

		$existing = DB::table('lesson_bookmarks')
			->where('user_id', $userId)
			->where('study_material_lesson_id', $lessonId)
			->first();

		if ($existing) {
			DB::table('lesson_bookmarks')->where('id', $existing->id)->delete();
			$bookmarked = false;
		} else {
			$now = date('Y-m-d H:i:s');
			DB::table('lesson_bookmarks')->insert([
				'user_id'                   => $userId,
				'study_material_lesson_id'  => $lessonId,
				'created_at'                => $now,
				'updated_at'                => $now,
			]);
			$bookmarked = true;
		}

		return response()->json([
			'success' => true,
			'data'    => ['lesson_id' => $lessonId, 'bookmarked' => $bookmarked],
		]);
	}

	/**
	 * The "My Bookmarks" page's "Bookmarked Lessons" section: each saved
	 * lesson with its book context, newest first.
	 */
	public function index(Request $request)
	{
		$rows = DB::table('lesson_bookmarks as lb')
			->join('study_material_lessons as l', 'l.id', '=', 'lb.study_material_lesson_id')
			->join('study_materials as b', 'b.id', '=', 'l.study_material_id')
			->where('lb.user_id', $request->user()->id)
			->orderByDesc('lb.id')
			->get([
				'l.id as lesson_id', 'l.title as lesson_title',
				'b.id as book_id', 'b.title as book_title', 'b.icon as book_icon', 'b.color as book_color',
				'lb.created_at as saved_at',
			])
			->map(function ($row) {
				$row->saved_at = $row->saved_at ? date('d M Y', strtotime($row->saved_at)) : null;
				return $row;
			});

		return response()->json([
			'success' => true,
			'message' => 'Data successfully found.',
			'data'    => $rows,
		]);
	}
}
