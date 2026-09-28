<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Str;

class CourseVideo extends Model
{
    protected $guarded = [];


    public function getPreviewAttribute($value) {
        if($value) {
            return asset(config('app.content_asset_url').'/public/course_video/'.$value);
        }
        return '';
    }

    public function getOriginalPreviewAttribute()
    {
       return $this->attributes['preview'] ?? null;
    }

    public function getBookAttribute($value) {
        if($value) {
            return asset(config('app.content_asset_url').'/public/course_video/'.$value);
        }
        return '';
    }

    public function getOriginalBookAttribute()
    {
       return $this->attributes['book'] ?? null;
    }
}
