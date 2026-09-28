<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
	/** Icon shown per notification type on the app's bell icon. */
	private $iconByType = [
		'mock_test'    => 'bi-clipboard-check',
		'live_video'   => 'bi-play-circle',
		'announcement' => 'bi-megaphone',
		'membership'   => 'bi-award',
		'general'      => 'bi-bell',
	];

	/**
	 * The learner's notification inbox, newest first, with the unread count
	 * the bell icon's badge needs.
	 */
	public function index(Request $request)
	{
		$userId = $request->user()->id;

		$items = Notification::where('user_id', $userId)
			->orderByDesc('id')
			->limit(50)
			->get()
			->map(function ($n) {
				return [
					'id'         => $n->id,
					'title'      => $n->title,
					'message'    => $n->message,
					'type'       => $n->type,
					'icon'       => $this->iconByType[$n->type] ?? $this->iconByType['general'],
					'is_read'    => $n->read_at !== null,
					'created_at' => $n->created_at ? $n->created_at->format('d M Y, h:i A') : null,
				];
			});

		$unreadCount = Notification::where('user_id', $userId)->whereNull('read_at')->count();

		return response()->json([
			'success' => true,
			'data'    => [
				'notifications' => $items,
				'unread_count'  => $unreadCount,
			],
		]);
	}

	/** Lightweight unread count only — cheap enough to call on every screen load for the bell badge. */
	public function unreadCount(Request $request)
	{
		$count = Notification::where('user_id', $request->user()->id)->whereNull('read_at')->count();

		return response()->json(['success' => true, 'data' => ['unread_count' => $count]]);
	}

	/** Mark one notification read. Only the owning user may mark it. */
	public function markRead(Request $request)
	{
		$userId = $request->user()->id;
		$id     = (int) $request->input('id', 0);

		$updated = Notification::where('id', $id)
			->where('user_id', $userId)
			->whereNull('read_at')
			->update(['read_at' => now()]);

		return response()->json(['success' => true, 'data' => ['updated' => (bool) $updated]]);
	}

	/** Mark every unread notification read for this user. */
	public function markAllRead(Request $request)
	{
		Notification::where('user_id', $request->user()->id)
			->whereNull('read_at')
			->update(['read_at' => now()]);

		return response()->json(['success' => true]);
	}
}
