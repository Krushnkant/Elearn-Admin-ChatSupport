<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class UserCourse extends Model
{
    use HasFactory;

    public function getCreatedAtDateAttribute($value) {
        return date('d/m/Y H:i a', strtotime($this->created_at));
    }
}
