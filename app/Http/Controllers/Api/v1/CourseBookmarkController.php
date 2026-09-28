<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Course;
use DB;

class CourseBookmarkController extends Controller
{
	/**
	 * Bookmarked course ids for the logged-in user — used to show the
	 * bookmark button's filled/empty state when a course page loads.
	 */
	public function ids(Request $request)
	{
		$ids = DB::table('course_bookmarks')
			->where('user_id', $request->user()->id)
			->pluck('course_id');

		return response()->json(['success' => true, 'data' => $ids]);
	}

	/**
	 * Add or remove a course bookmark. Returns the resulting state so the
	 * caller never has to guess which way the toggle went.
	 */
	public function toggle(Request $request)
	{
		$userId   = $request->user()->id;
		$courseId = (int) $request->input('course_id', 0);

		if (!$courseId || !Course::where('id', $courseId)->exists()) {
			return response()->json(['success' => false, 'message' => 'Unknown course.'], 422);
		}

		$existing = DB::table('course_bookmarks')
			->where('user_id', $userId)
			->where('course_id', $courseId)
			->first();

		if ($existing) {
			DB::table('course_bookmarks')->where('id', $existing->id)->delete();
			$bookmarked = false;
		} else {
			$now = date('Y-m-d H:i:s');
			DB::table('course_bookmarks')->insert([
				'user_id'    => $userId,
				'course_id'  => $courseId,
				'created_at' => $now,
				'updated_at' => $now,
			]);
			$bookmarked = true;
		}

		return response()->json([
			'success' => true,
			'data'    => ['course_id' => $courseId, 'bookmarked' => $bookmarked],
		]);
	}

	/**
	 * Bookmarked courses for the logged-in user, newest first — for the
	 * "My Bookmarks" page's course section.
	 *
	 * Built via Eloquent (not a raw DB::table join) specifically so
	 * Course::getImageAttribute() fires and returns a real asset URL — a
	 * plain join would hand back the bare filename instead, same class of
	 * bug already hit and fixed on the course e-book links.
	 */
	public function index(Request $request)
	{
		$bookmarks = DB::table('course_bookmarks')
			->where('user_id', $request->user()->id)
			->orderByDesc('id')
			->get(['course_id', 'created_at']);

		if ($bookmarks->isEmpty()) {
			return response()->json(['success' => true, 'message' => 'Data successfully found.', 'data' => []]);
		}

		$courses = Course::with('skill:id,name')
			->whereIn('id', $bookmarks->pluck('course_id'))
			->get()
			->keyBy('id');

		$rows = $bookmarks->map(function ($b) use ($courses) {
			$c = $courses->get($b->course_id);
			if (!$c) {
				return null;
			}
			return [
				'course_id'   => $c->id,
				'course_name' => $c->name,
				'image'       => $c->image,
				'skill_name'  => $c->skill->name ?? '',
				'saved_at'    => $b->created_at ? date('d M Y', strtotime($b->created_at)) : null,
			];
		})->filter()->values();

		return response()->json([
			'success' => true,
			'message' => 'Data successfully found.',
			'data'    => $rows,
		]);
	}
}
