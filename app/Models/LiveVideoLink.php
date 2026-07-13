<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveVideoLink extends Model
{
    protected $table = 'live_video_links';

    protected $guarded = [];

    public function liveVideo()
    {
        return $this->belongsTo(LiveVideo::class, 'live_video_id');
    }
}
