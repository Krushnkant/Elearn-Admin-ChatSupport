<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Str;

class ChapterVideo extends Model
{
    protected $guarded = [];
    protected $appends = ['video_duration','is_lock'];

    public function chapter()
    {
    	return $this->belongsTo(Chapter::class);
    }

    public function getVideoDurationAttribute()
    {
    	return "5:30:00";
    }
   
    public function getImageThumbAttribute($value)
    {
        if($value) {
            return asset('https://chatsupport.co.in/public/chapter/videos/thumbnail/'.$value);
        }
    	return asset('https://chatsupport.co.in/public/default-video.jpg');
    }

    public function getVideoAttribute($value) {
        if($value) {
            return asset('https://chatsupport.co.in/public/chapter/videos/'.$value);
        }
        return '';
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
