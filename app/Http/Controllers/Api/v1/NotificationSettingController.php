<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class NotificationSettingController extends Controller
{
	/**
	 * The logged-in user's notification preferences — defaults to all-off
	 * (matching the app's previous local-only default) when nothing has
	 * been saved yet.
	 */
	public function index(Request $request)
	{
		$row = DB::table('notification_settings')
			->where('user_id', $request->user()->id)
			->first();

		return response()->json([
			'success' => true,
			'data'    => [
				'email'                => $row ? (bool) $row->email : false,
				'push_notification'    => $row ? (bool) $row->push_notification : false,
				'updates_for_coaches'  => $row ? (bool) $row->updates_for_coaches : false,
				'announcement'         => $row ? (bool) $row->announcement : false,
				'stuff_you_enjoy'      => $row ? (bool) $row->stuff_you_enjoy : false,
			],
		]);
	}

	/**
	 * Upsert the logged-in user's notification preferences.
	 */
	public function update(Request $request)
	{
		$userId = $request->user()->id;

		$data = [
			'email'               => (bool) $request->input('email', false),
			'push_notification'   => (bool) $request->input('push_notification', false),
			'updates_for_coaches' => (bool) $request->input('updates_for_coaches', false),
			'announcement'        => (bool) $request->input('announcement', false),
			'stuff_you_enjoy'     => (bool) $request->input('stuff_you_enjoy', false),
			'updated_at'          => date('Y-m-d H:i:s'),
		];

		$exists = DB::table('notification_settings')->where('user_id', $userId)->exists();
		if ($exists) {
			DB::table('notification_settings')->where('user_id', $userId)->update($data);
		} else {
			$data['user_id']    = $userId;
			$data['created_at'] = date('Y-m-d H:i:s');
			DB::table('notification_settings')->insert($data);
		}

		return response()->json([
			'success' => true,
			'message' => 'Notification preferences updated.',
			'data'    => array_diff_key($data, ['user_id' => 1, 'created_at' => 1, 'updated_at' => 1]),
		]);
	}
}
