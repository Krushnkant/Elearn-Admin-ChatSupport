<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class Transaction extends Model
{
    use HasFactory;

    public function getCreatedAtDateAttribute($value) {
        return date('Y-m-d', strtotime($this->created_at));
    }

    public function getStartDateAttribute2($value) {
        return date('Y-m-d', strtotime($this->created_at));
    }

    public function getEndDateAttribute2($value) {
        return date('Y-m-d ', strtotime($this->created_at));
    }

    public function getPlanAttribute($value)
    {
        switch ($value) {
            default: return null;
            case 1: return 'Free';
            case 2: return 'Silver';
            case 3: return 'Gold';
        }
    }
}
