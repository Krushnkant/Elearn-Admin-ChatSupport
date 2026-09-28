<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LiveVideoLink;
use DB;

class LiveVideoSeenController extends Controller
{
	/**
	 * Watched live-video lesson ids for the logged-in user. The app uses
	 * this to restore per-lesson/day completion state after a restart.
	 */
	public function ids(Request $request)
	{
		$ids = DB::table('live_video_seens')
			->where('user_id', $request->user()->id)
			->pluck('live_video_link_id');

		return response()->json(['success' => true, 'data' => $ids]);
	}

	/**
	 * Marks a live-video lesson as watched for the logged-in user.
	 * Idempotent — calling it again for the same lesson is a no-op.
	 */
	public function store(Request $request)
	{
		$userId = $request->user()->id;
		$linkId = (int) $request->input('link_id', 0);

		if (!$linkId || !LiveVideoLink::where('id', $linkId)->exists()) {
			return response()->json(['success' => false, 'message' => 'Unknown video.'], 422);
		}

		$exists = DB::table('live_video_seens')
			->where('user_id', $userId)
			->where('live_video_link_id', $linkId)
			->exists();

		if (!$exists) {
			$now = date('Y-m-d H:i:s');
			DB::table('live_video_seens')->insert([
				'user_id'             => $userId,
				'live_video_link_id'  => $linkId,
				'created_at'          => $now,
				'updated_at'          => $now,
			]);
		}

		return response()->json([
			'success' => true,
			'message' => 'Video seen recorded.',
			'data'    => ['link_id' => $linkId],
		]);
	}
}
