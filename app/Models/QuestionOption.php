<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $hidden = ['question_id'];
    protected $guarded = [];

    public function getImageAttribute($value) {
    	if($value) {
    		return asset('https://chatsupport.co.in/public/options/'.$value);
    	}
    	return asset('https://chatsupport.co.in/public/no-image.png/');
    }
}
