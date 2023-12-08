<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Str;

class Chapter extends Model
{
    protected $guarded = [];
    //protected $appends = ['lock'];


    public function course()
    {
    	return $this->belongsTo(Course::class);
    }

    public function videos()
    {
    	return $this->hasMany(ChapterVideo::class);
    }

    public function getChapterAttribute($value)
    {
    	return "Chapter ".$value;
    }

    public function getOriginalChapterAttribute()
	{
	    return $this->attributes['chapter'];
	}
}
