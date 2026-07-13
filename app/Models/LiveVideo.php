<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveVideo extends Model
{
    protected $table = 'live_videos';

    protected $guarded = [];

    /**
     * Individual video links that belong to this day, ordered for display.
     */
    public function links()
    {
        return $this->hasMany(LiveVideoLink::class, 'live_video_id')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Active video links only (what students should see).
     */
    public function activeLinks()
    {
        return $this->links()->where('status', 1);
    }

    /**
     * Effective video count: the number of active links if any exist,
     * otherwise fall back to the manually stored video_count.
     */
    public function getEffectiveVideoCountAttribute()
    {
        $linkCount = $this->activeLinks()->count();
        return $linkCount > 0 ? $linkCount : (int) $this->video_count;
    }

    /**
     * Human-friendly scheduled time for the frontend (null when on-demand only).
     */
    public function getScheduledLabelAttribute()
    {
        if (empty($this->scheduled_at)) {
            return null;
        }
        return date('d M Y, h:i A', strtotime($this->scheduled_at));
    }

    /**
     * Whether the live session is still upcoming.
     */
    public function getIsUpcomingAttribute()
    {
        return !empty($this->scheduled_at) && strtotime($this->scheduled_at) > time();
    }
}
