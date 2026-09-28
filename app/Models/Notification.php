<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $guarded = [];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Create a notification for one user. The single place every real event
     * (mock test scored, new live video, admin announcement, ...) should go
     * through, so the shape stays consistent everywhere it's raised from.
     */
    public static function notify($userId, $title, $message = null, $type = 'general')
    {
        return static::create([
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'type'    => $type,
        ]);
    }
}
