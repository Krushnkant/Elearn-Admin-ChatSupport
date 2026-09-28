<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StudyMaterialLesson;
use DB;

class LessonNoteController extends Controller
{
	/**
	 * With ?lesson_id=: that lesson's notes only (newest first), for the
	 * courseReader popup. Without it: every note this user has saved across
	 * all lessons, joined with book/lesson titles, for the My Notes page.
	 */
	public function index(Request $request)
	{
		$userId   = $request->user()->id;
		$lessonId = (int) $request->query('lesson_id', 0);

		$query = DB::table('lesson_notes as n')
			->join('study_material_lessons as l', 'l.id', '=', 'n.study_material_lesson_id')
			->join('study_materials as b', 'b.id', '=', 'l.study_material_id')
			->where('n.user_id', $userId)
			->orderByDesc('n.id');

		if ($lessonId) {
			$query->where('n.study_material_lesson_id', $lessonId);
		}

		$rows = $query->get([
				'n.id', 'n.note',
				'l.id as lesson_id', 'l.title as lesson_title',
				'b.id as book_id', 'b.title as book_title', 'b.icon as book_icon', 'b.color as book_color',
				'n.created_at as saved_at',
			])
			->map(function ($row) {
				$row->saved_at = $row->saved_at ? date('d M Y, h:i A', strtotime($row->saved_at)) : null;
				return $row;
			});

		return response()->json([
			'success' => true,
			'message' => 'Data successfully found.',
			'data'    => $rows,
		]);
	}

	/**
	 * Save a new note against a lesson. Multiple notes per lesson are allowed.
	 */
	public function store(Request $request)
	{
		$userId   = $request->user()->id;
		$lessonId = (int) $request->input('lesson_id', 0);
		$note     = trim((string) $request->input('note', ''));

		if (!$lessonId || !StudyMaterialLesson::where('id', $lessonId)->exists()) {
			return response()->json(['success' => false, 'message' => 'Unknown lesson.'], 422);
		}
		if ($note === '') {
			return response()->json(['success' => false, 'message' => 'Note cannot be empty.'], 422);
		}

		$now = date('Y-m-d H:i:s');
		$id = DB::table('lesson_notes')->insertGetId([
			'user_id'                  => $userId,
			'study_material_lesson_id' => $lessonId,
			'note'                     => $note,
			'created_at'               => $now,
			'updated_at'               => $now,
		]);

		return response()->json([
			'success' => true,
			'data'    => ['id' => $id, 'note' => $note, 'saved_at' => date('d M Y, h:i A', strtotime($now))],
		]);
	}

	/**
	 * Delete a note. Only the owning user may remove it.
	 */
	public function destroy(Request $request)
	{
		$userId = $request->user()->id;
		$id     = (int) $request->input('id', 0);

		$deleted = DB::table('lesson_notes')->where('id', $id)->where('user_id', $userId)->delete();

		if (!$deleted) {
			return response()->json(['success' => false, 'message' => 'Note not found.'], 404);
		}

		return response()->json(['success' => true]);
	}
}
