<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryQuestion extends Model
{
    use HasFactory;
    protected $table = 'category_questions';
    protected $guarded = [];
    public $timestamps = false;
    protected $fillable = [
        'id',
        'question_id',
        'category_id',
        'sub_category_id',
       
    ];

    // public function category()
    // {
    //     return $this->belongsTo(Category::class);
    // }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function category()
    {
      return $this->belongsTo(Category::class,'sub_category_id','id');
      
    }
   
    public function categoryByType()
    {
      return $this->hasMany(Category::class,'type','category_id','id','sub_category_id');
    }
    public function answers()
    {
      return $this->hasMany(Answer::class);
    }
}
