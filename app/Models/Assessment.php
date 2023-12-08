<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $guarded = [];

    public function questions()
    {
    	return $this->hasMany(Question::class, 'id');
    }

    public function course()
    {
    	return $this->belongsTo(Course::class);
    }

    public function skill()
    {
    	return $this->belongsTo(Skill::class);
    }

    public function getImageAttribute($value) {
        if($value) {
            return asset('https://chatsupport.co.in/public/assessments/'.$value);
        }
        return asset('https://chatsupport.co.in/public/default-user.jpg');
    }

    public function getOriginalImageAttribute()
    {
       return $this->attributes['image'];
    }

    public function getSetsAttribute( $plan ) {

        $sets = [];
        $noOfQuestion = 1;
        if($plan == "Silver") {
            $noOfQuestion = 5;
        } else if($plan == "Gold") {
            $noOfQuestion = 10;
        }
        

        for ($i=1; $i<=$noOfQuestion; $i++) {
            $sets[] = [
                "setIndex" => $i,
                "title" => $this->title. ' - Set ' . $i,
                "duration" => 240,
                "skill" => "Professional"
            ];
        }

        return $sets;

    }

}
