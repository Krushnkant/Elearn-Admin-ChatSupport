<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
	protected $guarded = [''];
	protected $table = 'cources';

	public function skill()
	{
		return $this->belongsTo(Skill::class);
	}

	public function chapters()
	{
		return $this->hasMany(Chapter::class);
	}

	public function assessments()
	{
		return $this->hasMany(Assessment::class);
	}

	public function books()
	{
		return $this->hasMany(CourseVideo::class);
	}

 	public function getImageAttribute($value) {
		if($value) {
			return asset('https://chatsupport.co.in/public/course/'.$value);
		}
		return asset('https://chatsupport.co.in/public/no-image.png/');
	}

	public function getOriginalImageAttribute()
	{
	   return $this->attributes['image'];
	}

  public function getTrainnigModeAttribute($value)
  {
    return $value == 1 ? "Flexible" : "";
  }

  public function getOriginalTrainnigModeAttribute()
  {
     return $this->attributes['trainnig_mode'];
  }
}
