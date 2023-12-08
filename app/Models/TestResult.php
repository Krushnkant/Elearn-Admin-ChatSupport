<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function answer()
    {
    	return $this->hasOne(Answer::class, 'mock_test_id');
    }

    public function assessment()
    {
    	return $this->hasOne(Assessment::class, 'id', 'assessment_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'id');
    }

    public function getCreatedAtAttribute($value) {
        return date('d/m/Y H:i:s A', strtotime($value));
    }
   

}
