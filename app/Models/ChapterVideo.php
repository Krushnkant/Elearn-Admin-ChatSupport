<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Str;

class ChapterVideo extends Model
{
    protected $guarded = [];
    protected $appends = ['is_lock'];

    public function chapter()
    {
    	return $this->belongsTo(Chapter::class);
    }


    public function getImageThumbAttribute($value)
    {
        if($value) {
            return asset(config('app.content_asset_url').'/public/chapter/videos/thumbnail/'.$value);
        }
    	return asset(config('app.content_asset_url').'/public/default-video.jpg');
    }

    public function getVideoAttribute($value) {
        if(!$value) {
            return '';
        }
        // A pasted link (YouTube, Vimeo, CDN, ...) is stored as-is; an
        // uploaded file is stored as just its filename, so it needs the
        // storage path prefixed.
        if (preg_match('/^https?:\/\//i', $value)) {
            return $value;
        }
        return asset(config('app.content_asset_url').'/public/chapter/videos/'.$value);
    }

    public function userVideos()
    {
        return $this->hasMany(UserVideo::class, 'chapter_video_id', 'id');
    }

    public function getIsLockAttribute()
    {
        $data = $this->userVideos()->first();
        if($data) {
            return false;
        }
        return true;
    }

    public function getOriginalVideoAttribute()
    {
       return $this->attributes['video'];
    }


    // public function getOriginalImageThumbAttribute()
    // {
    //     if($value) {
    //         return asset('public/chapter/videos/thumbnail/'.$value);
    //     }
    // 	return asset('public/default-video.jpg');
    // }

    public function getOriginalImageThumbAttribute()
    {
       return $this->attributes['image_thumb'];
    }
    
    /*public function users()
    {
        return $this->belongsToMany(User::class);
    }*/

    /*public function getLockAttribute()
    {
        $data = $this->users()
        foreach ($user->roles as $role) {
            echo $role->pivot->created_at;
        }
    }*/
    public function getCreatedAtAttribute($date) {
        return date('Y-m-d H:i:s', strtotime($date));
    }
    public function getUpdatedAtAttribute($date) {
        return date('Y-m-d H:i:s', strtotime($date));
    }
    

}
