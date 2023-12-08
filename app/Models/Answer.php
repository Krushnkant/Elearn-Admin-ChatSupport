<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;
    const CORRECT = 'correct';
    const INCORRECT = 'incorrect';
    const UNATTEMPTED = 'unattempted';
    protected $fillable = ['mock_test_id', 'question_id', 'answer_id','is_correctans'];

    public function question()
    {
    	return $this->belongsTo(Question::class);
    }
   
    public function category()
    {     
        
        return $this->hasManyThrough(
        Category::class,
        Question::class,
        'category_id', // Foreign key on the environments table...
        'type', // Foreign key on the deployments table...
        'id', // Local key on the projects table...
        'category_id' // Local key on the environments table...
    );
    }
    
    public function categoryQuestion()
    {
      return $this->belongsTo(CategoryQuestion::class, 'question_id');
    }
   

    function getIsCorrectAttribute($value) {
        if($this->answer_id > 0) {
            return ($value > 0)? self::CORRECT : self::INCORRECT;
        }
        return self::UNATTEMPTED;
    }

    public function questionOption()
    {
        return $this->hasOne(QuestionOption::class, 'id', 'answer_id');
    }
    
    public function questionOptionNew()
    {
        return $this->hasMany(QuestionOption::class, 'question_id', 'question_id');
    }

    public function answer() {
        return $this->hasOne(QuestionOption::class, 'id', 'answer_id');
    }

    public function testResult() {
        return $this->belongsTo(TestResult::class, 'mock_test_id', 'id');
    }

    public function getCreatedAtDateAttribute($value) {
        return date('d/m/Y H:i a', strtotime($this->created_at));
    }

    public function getPassingPercentageAttribute($value) {
        return "70%";
    }

    /*public function getStatusAttribute($value) {
        return "Fail";
    }*/

}
