<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
  // protected $guarded = [''];

  protected $fillable = [
    'set_type',
    'category_id',
    'category_id',
    'sub_category_id',
    'course_id',
    'assessment_id',
    'title',
    'explanation',
    'dificulty_level',
    'marks',
    'question_type',
    'status',
    'created_at'
];
  
  public function category()
  {
    return $this->belongsTo(Category::class,'sub_category_id','id');
    
  }
 
  public function categoryByType()
  {
    return $this->hasMany(Category::class,'type','category_id','id','sub_category_id');
  }


  public function questionOptions()
  {
    return $this->hasMany(QuestionOption::class);
  }

  public function answers()
  {
    return $this->hasMany(Answer::class);
  }

  public function categoryQuestion()
  {
    return $this->hasMany(CategoryQuestion::class, 'question_id');
  }

  public function course()
  {
    return $this->belongsTo(Course::class);
  }

  public function assessment()
  {
    return $this->belongsTo(Assessment::class);
  }

  public function getQuestionTypeAttribute($value)
  {
    return $value == 1 ? "Single" : "Multiple";
  }

  public function getIsCorrectAnswerAttribute($value)
  {
    return $value == 1 ? "Yes" : "No";
  }

  public function scopeCategory($query, $category_id) {
    return $query->whereHas('category', function($q) use ($category_id){
      $q->where('id', $category_id);
    });
  }

  public function getCorrectAttribute()
  {
    return $this->questionOptions()->where('is_correct', 1)->count();
    // return $query->whereHas('questionOptions', function($q) {
    //   $q->where('is_correct', 1);
    // });
  }

   public function type()
  {
    return $this->belongsToMany(Category::class,'type');
   }

}
