<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LiveVideo;

class LiveVideoController extends Controller
{
	/**
	 * Active live-video sessions for the student Live Videos page,
	 * ordered by day.
	 */
	public function index(Request $request)
	{
		$sessions = LiveVideo::where('status', 1)
			->with(['activeLinks'])
			->orderBy('day_no', 'asc')
			->get()
			->map(function ($lv) {
				$videos = $lv->activeLinks->map(function ($link) {
					return [
						'id'            => $link->id,
						'title'         => $link->title,
						'video_url'     => $link->video_url,
						'duration_mins' => (int) $link->duration_mins,
					];
				})->values();

				$linksDuration = (int) $lv->activeLinks->sum('duration_mins');

				return [
					'id'             => $lv->id,
					'day_no'         => $lv->day_no,
					'title'          => $lv->title,
					'description'    => $lv->description,
					// Derived: number of real videos if any, else the stored number.
					'video_count'    => $videos->count() > 0 ? $videos->count() : (int) $lv->video_count,
					// Derived: sum of the videos' durations if any, else the stored value.
					'duration_mins'  => $videos->count() > 0 ? $linksDuration : (int) $lv->duration_mins,
					'video_url'      => $lv->video_url,
					'scheduled_at'   => $lv->scheduled_at,
					'scheduled_label'=> $lv->scheduled_label,
					'is_upcoming'    => $lv->is_upcoming,
					'videos'         => $videos,
				];
			});

		return response()->json([
			'success' => true,
			'message' => "Data successfully found.",
			'data'    => $sessions,
		]);
	}
}
