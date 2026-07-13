<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StudyMaterial;

class StudyMaterialController extends Controller
{
	/**
	 * Active study-material books for the student Study Material page,
	 * ordered for display.
	 */
	public function index(Request $request)
	{
		$books = StudyMaterial::where('status', 1)
			->with(['activeLessons'])
			->orderBy('sort_order', 'asc')
			->get()
			->map(function ($b) {
				$lessons = $b->activeLessons->map(function ($l) {
					$type = $l->file ? 'pdf' : ($l->content ? 'html' : 'none');
					return [
						'id'       => $l->id,
						'title'    => $l->title,
						'type'     => $type,
						'content'  => $l->content,
						'file_url' => $l->file_url,
					];
				})->values();

				return [
					'id'          => $b->id,
					'title'       => $b->title,
					'description' => $b->description,
					'icon'        => $b->icon,
					'color'       => $b->color,
					'lessons'     => $lessons,
				];
			});

		return response()->json([
			'success' => true,
			'message' => "Data successfully found.",
			'data'    => $books,
		]);
	}
}
